USE masonsroom;

INSERT INTO topics(topic_name, topic_img) VALUES ("fruit","fruit.gif"),("usa","usa.gif"),("canada","canada.gif"),("global","global.gif"),("anime","anime.gif"),("movies","movie.gif"),("drinking","drinking.gif"),("drugs","drugs.gif"),("business","business.gif"),("software","software.gif"),("wall Street","wallStreet.gif"),("cars","cars.gif"),("music","music.gif"),("tv","tv.gif");

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `is_admin`,profile_pic) VALUES (NULL, 'Kirbyfan101', 'kirby123', 'kirby@fake.com', '0','1.png');

INSERT INTO `posts` (`post_id`, `topic_id`, `user_id`, `content`, `created_at`, `pinned`, `post_title`) VALUES (NULL, '12', '2', 'Hey everyone! So i visited the local aquarium the other day and during the live show I picked up one of those rain overcoats. They work surprisingly well to stop your clothes from getting covered in oil! Try this out for yourself and let me know what you think :p', '2023-03-01 21:44:43', '0', 'How to change your oil without getting dirty!');

INSERT INTO `posts` (`post_id`, `topic_id`, `user_id`, `content`, `created_at`, `pinned`, `score`, `post_title`) VALUES (NULL, '12', '1', '1. Don\'t be an idiot', '2023-03-02 14:03:51', '1', '0', 'READ BEFORE POSTING');

