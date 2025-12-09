INSERT INTO bills (
    customer_id,
    meter_id,
    meter_reading_id,
    bill_number,
    billing_period_start,
    billing_period_end,
    consumption,
    base_charge,
    consumption_charge,
    tax_amount,
    late_fee,
    total_amount,
    paid_amount,
    due_date,
    bill_status,
    created_at,
    updated_at
)
SELECT
    m.customer_id,
    m.id,
    NULL,
    CONCAT('BILL-', LPAD(m.id, 6, '0')),
    NULL,
    NULL,
    0,
    0,
    0,
    0,
    0,
    m.current_balance,
    0,
    DATE_ADD(CURDATE(), INTERVAL 14 DAY),
    'unpaid',
    NOW(),
    NOW()
FROM meters m
WHERE m.current_balance > 0
  AND m.customer_id IS NOT NULL
  AND NOT EXISTS (
        SELECT 1 FROM bills b
        WHERE b.meter_id = m.id
    );
