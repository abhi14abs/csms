DELIMITER $$

DROP PROCEDURE IF EXISTS ProcessMonthlyDeduction$$

CREATE PROCEDURE ProcessMonthlyDeduction(
    IN p_employee_number VARCHAR(20),
    IN p_subscription_amount DECIMAL(15,2),
    IN p_emi_amount DECIMAL(15,2),
    IN p_transaction_date DATE
)
BEGIN
    DECLARE v_member_id INT;
    DECLARE v_share_acc_id INT;
    DECLARE v_savings_acc_id INT;
    DECLARE v_loan_acc_id INT;
    DECLARE v_share_balance DECIMAL(15,2);
    DECLARE v_gap_to_10k DECIMAL(15,2);

    SELECT member_id INTO v_member_id FROM members WHERE employee_number = p_employee_number;

    SELECT account_id, current_balance INTO v_share_acc_id, v_share_balance 
    FROM accounts WHERE member_id = v_member_id AND account_type = 'SHARE' LIMIT 1;

    SELECT account_id INTO v_savings_acc_id 
    FROM accounts WHERE member_id = v_member_id AND account_type = 'SAVINGS' LIMIT 1;

    IF v_share_balance >= 10000 THEN
        INSERT INTO transactions (account_id, amount, tx_type, category, description, tx_date)
        VALUES (v_savings_acc_id, p_subscription_amount, 'CREDIT', 'SUBSCRIPTION', 'Monthly Sub (Diversion)', p_transaction_date);

        UPDATE accounts SET current_balance = current_balance + p_subscription_amount WHERE account_id = v_savings_acc_id;
    ELSE
        SET v_gap_to_10k = 10000 - v_share_balance;

        IF p_subscription_amount <= v_gap_to_10k THEN
            INSERT INTO transactions (account_id, amount, tx_type, category, description, tx_date)
            VALUES (v_share_acc_id, p_subscription_amount, 'CREDIT', 'SUBSCRIPTION', 'Monthly Sub', p_transaction_date);

            UPDATE accounts SET current_balance = current_balance + p_subscription_amount WHERE account_id = v_share_acc_id;
        ELSE
            INSERT INTO transactions (account_id, amount, tx_type, category, description, tx_date)
            VALUES (v_share_acc_id, v_gap_to_10k, 'CREDIT', 'SUBSCRIPTION', 'Monthly Sub (Split-Share)', p_transaction_date);
            UPDATE accounts SET current_balance = current_balance + v_gap_to_10k WHERE account_id = v_share_acc_id;

            INSERT INTO transactions (account_id, amount, tx_type, category, description, tx_date)
            VALUES (v_savings_acc_id, (p_subscription_amount - v_gap_to_10k), 'CREDIT', 'SUBSCRIPTION', 'Monthly Sub (Split-Sav)', p_transaction_date);
            UPDATE accounts SET current_balance = current_balance + (p_subscription_amount - v_gap_to_10k) WHERE account_id = v_savings_acc_id;
        END IF;
    END IF;

    SELECT account_id INTO v_loan_acc_id FROM accounts 
    WHERE member_id = v_member_id AND account_type = 'LOAN' AND status = 'Active' LIMIT 1;

    IF v_loan_acc_id IS NOT NULL AND p_emi_amount > 0 THEN
        INSERT INTO transactions (account_id, amount, tx_type, category, description, tx_date)
        VALUES (v_loan_acc_id, p_emi_amount, 'CREDIT', 'EMI', 'Monthly EMI Deduction', p_transaction_date);

        UPDATE accounts SET current_balance = current_balance - p_emi_amount WHERE account_id = v_loan_acc_id;
    END IF;

END$$

DELIMITER ;
