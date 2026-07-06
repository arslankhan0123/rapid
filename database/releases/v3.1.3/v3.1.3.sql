ALTER TABLE expenses CHANGE customer_id customer_id INT UNSIGNED DEFAULT NULL;
alter table `expenses` drop foreign key `expenses_customer_id_foreign`;
alter table `expenses` add constraint `expenses_customer_id_foreign` foreign key (`customer_id`) references `customers` (`id`) on delete cascade on update cascade;
