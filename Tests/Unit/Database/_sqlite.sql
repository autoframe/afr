drop table if exists `comment`;
drop table if exists article;
drop table if exists `user`;
drop table if exists country;

create table country
(
    id      integer
        constraint id
            primary key autoincrement,
    name    TEXT not null,
    details TEXT not null
);

INSERT INTO `country` (`id`, `name`, `details`) VALUES
 (1, 'Slovakia', '{"name": "Slovensko", "pop": 5456300, "gdp": 90.75}'),
 (2, 'Canada', '{"name": "Canada", "pop": 37198400, "gdp": 1592.37}'),
 (3, 'Germany', '{"name": "Deutschland", "pop": 82385700, "gdp": 3486.12}');


create table user
(
    id         integer
        constraint user_pk
            primary key autoincrement,
    country_id integer not null
        constraint user_country_null_fk
            references country (id),
    type       TEXT    not null,
    name       TEXT    not null
);
INSERT INTO `user` (`id`, `country_id`, `type`, `name`)
VALUES (1, 1, 'admin', 'Marek'),
       (2, 1, 'author', 'Robert'),
       (3, 2, 'admin', 'Chris'),
       (4, 2, 'author', 'Kevin');
	   

create table article
(
    id         integer
        constraint article_pk
            primary key autoincrement,
    user_id integer not null
        constraint article_user_null_fk
            references user (id),
    published_at       TEXT    not null,
    title       TEXT    not null,
    content       TEXT    not null
);


INSERT INTO `article` (`id`, `user_id`, `published_at`, `title`, `content`) VALUES
(1, 1, '2011-12-10 12:10:00', 'article 1', 'content 1'),
(2, 2, '2011-12-20 16:20:00', 'article 2', 'content 2'),
(3, 1, '2012-01-04 22:00:00', 'article 3', 'content 3'),
(4, 4, '2018-07-07 15:15:07', 'artïcle 4', 'content 4'),
(5, 3, '2018-10-01 01:10:01', 'article 5', 'content 5'),
(6, 3, '2019-01-21 07:00:00', 'სარედაქციო 6', '함유량 6');

create table `comment`
(
    id         integer
        constraint comment_pk
            primary key autoincrement,
	article_id integer not null
        constraint comment_article_null_fk
            references article (id),
    user_id integer not null
        constraint comment_user_null_fk
            references user (id),
    content       TEXT    not null
);




INSERT INTO `comment` (`id`, `article_id`, `user_id`, `content`)
VALUES (1, 1, 1, 'comment 1.1'),
       (2, 1, 2, 'comment 1.2'),
       (3, 2, 1, 'comment 2.1'),
       (4, 5, 4, 'cömment 5.4'),
       (5, 6, 2, 'ਟਿੱਪਣੀ 6.2');
