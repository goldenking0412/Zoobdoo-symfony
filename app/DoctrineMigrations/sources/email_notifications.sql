INSERT INTO `email_notifications` (`id`, `code`, `name`, `subject`, `body`, `tokens`)
			VALUES
				(1,'SERVICE_REQUESTS','Service Request','New service request','New service request','{"0":"#url#"}'),
				(2,'FORUM_TOPICS','Forum topics','New message on forum','New message on forum','{"0":"#url#"}'),
				(3,'PROFILE_MESSAGES','Profile messages','New message in profile','New message in profile','{"0":"#url#"}');
