import pandas as pd
import mysql.connector

conn = mysql.connector.connect(
    host='localhost',
    user='root',
    password='',  # Set if needed
    database='dreamride'
)

query = """
SELECT 
    upi.Email,
    upi.Product_id,
    upi.Interaction_type,
    p.Company_name,
    COALESCE(b.Prize, s.Prize, h.Prize, e.Prize) AS Prize
FROM user_product_interaction upi
LEFT JOIN product p ON upi.Product_id = p.Product_id
LEFT JOIN bike b ON upi.Product_id = b.Product_id
LEFT JOIN scooter s ON upi.Product_id = s.Product_id
LEFT JOIN helmet h ON upi.Product_id = h.Product_id
LEFT JOIN engine_oil e ON upi.Product_id = e.Product_id
"""

df = pd.read_sql(query, conn)

df['score'] = df['Interaction_type'].apply(lambda x: 3 if x == 'book' else 1)
df['Prize'] = pd.to_numeric(df['Prize'], errors='coerce')
df = df.dropna(subset=['Prize'])

product_info = df[['Product_id', 'Prize', 'Company_name']].drop_duplicates()

cursor = conn.cursor()
cursor.execute("""
    SELECT p.Product_id, p.Company_name,
        COALESCE(b.Prize, s.Prize, h.Prize, e.Prize) AS Prize
    FROM product p
    LEFT JOIN bike b ON p.Product_id = b.Product_id
    LEFT JOIN scooter s ON p.Product_id = s.Product_id
    LEFT JOIN helmet h ON p.Product_id = h.Product_id
    LEFT JOIN engine_oil e ON p.Product_id = e.Product_id
""")
all_products = pd.DataFrame(cursor.fetchall(), columns=['Product_id', 'Company_name', 'Prize'])
all_products['Prize'] = pd.to_numeric(all_products['Prize'], errors='coerce')
all_products = all_products.dropna()

recommendations = []
user_groups = df.groupby('Email')

for email, user_df in user_groups:
    seen_products = user_df['Product_id'].unique()
    seen_avg_price = user_df['Prize'].mean()
    seen_companies = user_df['Company_name'].unique()

    # Step 1: Filter out already interacted products
    candidates = all_products[~all_products['Product_id'].isin(seen_products)].copy()

    # Step 2: Price proximity filter (±50,000)
    price_candidates = candidates[candidates['Prize'].between(seen_avg_price - 50000, seen_avg_price + 50000)].copy()

    # Step 3: Company fallback if needed
    company_candidates = candidates[candidates['Company_name'].isin(seen_companies)].copy()

    # Step 4: Fill the rest with any nearest priced product
    candidates['price_diff'] = abs(candidates['Prize'] - seen_avg_price)

    # Combine and ensure no duplicates
    combined = pd.concat([price_candidates, company_candidates]).drop_duplicates('Product_id')
    combined['price_diff'] = abs(combined['Prize'] - seen_avg_price)
    top = combined.sort_values('price_diff').head(4)

    # If fewer than 4, fill from global nearest
    if len(top) < 4:
        missing = 4 - len(top)
        filler = candidates[~candidates['Product_id'].isin(top['Product_id'])].sort_values('price_diff').head(missing)
        top = pd.concat([top, filler])

    # Final formatting
    top['Email'] = email
    top = top[['Email', 'Product_id', 'Prize']].rename(columns={'Product_id': 'Recommended_Product_id', 'Prize': 'Score'})
    recommendations.append(top)

result_df = pd.concat(recommendations, ignore_index=True)

print("✅ Sample recommendations:")
print(result_df.head(10))

cursor.execute("SELECT Email FROM signup")
valid_emails = set(email[0] for email in cursor.fetchall())

cursor.execute("SELECT Product_id FROM product")
valid_products = set(pid[0] for pid in cursor.fetchall())

insert_count = 0

for email in result_df['Email'].unique():
    user_recs = result_df[result_df['Email'] == email]

    if email not in valid_emails:
        print(f"⚠️ Skipping {email} — not in signup")
        continue

    cursor.execute("DELETE FROM product_recommendations WHERE Email = %s", (email,))

    for _, row in user_recs.iterrows():
        product_id = row['Recommended_Product_id']
        if product_id not in valid_products:
            print(f"⚠️ Invalid Product: {product_id} for {email}")
            continue

        try:
            cursor.execute("""
                INSERT INTO product_recommendations (Email, Recommended_Product_id, Score)
                VALUES (%s, %s, %s)
            """, (email, product_id, float(row['Score'])))
            insert_count += 1
        except Exception as e:
            print(f"❌ DB Error for {email} → {product_id}: {e}")

conn.commit()
conn.close()

print(f"✅ {insert_count} recommendations inserted successfully.")
