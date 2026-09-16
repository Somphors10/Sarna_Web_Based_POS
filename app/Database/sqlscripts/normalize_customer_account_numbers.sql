-- Normalize CUST-* customer account numbers to CUST-00001 (5-digit) format.
-- Safe to run on live tenant databases. Adjust table prefix if not wbpos_.

UPDATE wbpos_customers
SET account_number = CONCAT(
        'CUST-',
        LPAD(CAST(SUBSTRING(account_number, 6) AS UNSIGNED), 5, '0')
    )
WHERE account_number REGEXP '^CUST-[0-9]+$'
  AND account_number NOT REGEXP '^CUST-[0-9]{5}$';
