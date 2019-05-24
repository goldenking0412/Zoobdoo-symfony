LOCK TABLES `images` WRITE;

INSERT INTO `images` (`id`, `name`, `path`, `created_date`, `updated_date`)
VALUES
	(1, 'slide1.png','assets/images/homepage','2015-11-23 15:00:02','2015-11-23 15:22:59'),
	(2, 'slide2.png','assets/images/homepage','2015-11-23 15:27:30','2015-11-23 15:27:30'),
	(3, 'slide3.png','assets/images/homepage','2015-11-23 15:28:10','2015-11-23 15:28:10');
    
UNLOCK TABLES;

LOCK TABLES `homepage_slides` WRITE;

INSERT INTO `homepage_slides` (`image_id`, `title`, `text`, `created_date`, `updated_date`)
VALUES
	(1,'A place where <span class=\"bold-text\">Tenants</span> and <span class=\"bold-text\">Landlords</span> can connect','<h2 class=\"bold-text\">Ever wish you could</h2>\n                                        <ul><li><span class=\"fa fa-chevron-right\"></span>Have all the capabilities of a\n                                                property manager\n                                            </li>\n                                            <li><span class=\"fa fa-chevron-right\"></span>Pay your rent online</li>\n                                            <li><span class=\"fa fa-chevron-right\"></span>Browse open units to rent</li>\n                                        </ul><span class=\"bold-text can-text\">Now you can</span>','2015-11-23 15:00:02','2015-11-23 15:22:59'),
	(2,'<span class=\"bold-text\">I used to</span> use paper filing or property managers.','<p>\n                                            I wanted something secure, efficient, easy to manage and affordable. <span class=\"bold-text\">With eRentPay now everything I need is at my fingertips, saving me so much time and money.</span>\n                                        </p>','2015-11-23 15:27:30','2015-11-23 15:27:30'),
	(3,'With our schedules <span class=\"bold-text\">we needed a simpler way</span>','<p>We’re no longer worried about pesky things like running out of stamps to pay\n                                            the bill or playing phone tag to track down our landlord.\n                                            <span class=\"bold-text\">We do it all online now at eRentPay!</span></p>','2015-11-23 15:28:10','2015-11-23 15:28:10');

UNLOCK TABLES;