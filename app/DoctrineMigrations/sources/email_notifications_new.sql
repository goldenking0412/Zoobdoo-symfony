INSERT INTO `email_notifications` (`type`, `name`, `subject`, `title`, `button`, `body`, `tokens`)
			VALUES
				('SERVICE_REQUESTS','Service Request','eRentPay - New Service Request','You have received a new service request from Tenant!', 'Click to see','','{"0":"#url#"}'),
				('FORUM_TOPICS','Forum message','eRentPay - New Forum Message','There is a new message on Forum!','Click to see','','{"0":"#url#"}'),
				('PROFILE_MESSAGES','Profile messages','eRentPay - New Profile Message','You have received a new message from Tenant.','Click to see','','{"0":"#url#"}');
