
    INSERT INTO `golf-locker-transaction`(`locker-id`, `due-date`, `name`, `telephone`, `deposit`, `amount`, `lock-number`, `lock-price`, `month`, `datetime`, `remark`, `auth`, `src`) 
    select '123', '2025-12-31', 'Ms Chen', '56612366', '0', '1050', '', '0', '', CURRENT_TIMESTAMP(), '', '', 'winnie'
    where '2025-12-31'>current_timestamp()
    ;
    