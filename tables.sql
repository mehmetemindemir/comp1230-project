create table Users
(
    id       int auto_increment
        primary key,
    username varchar(50)  not null,
    email    varchar(100) not null,
    password varchar(255) not null,
    constraint email
        unique (email),
    constraint username
        unique (username)
);

create table Topics
(
    id          int auto_increment
        primary key,
    user_id     int                                   not null,
    title       varchar(255)                          not null,
    description text                                  null,
    created_at  timestamp default current_timestamp() not null,
    constraint Topics_ibfk_1
        foreign key (user_id) references Users (id)
);

create table Comments
(
    id           int auto_increment
        primary key,
    user_id      int                                   not null,
    topic_id     int                                   not null,
    comment      text                                  not null,
    commented_at timestamp default current_timestamp() not null,
    constraint Comments_ibfk_1
        foreign key (user_id) references Users (id),
    constraint Comments_ibfk_2
        foreign key (topic_id) references Topics (id)
);

create index topic_id
    on Comments (topic_id);

create index user_id
    on Comments (user_id);

create index user_id
    on Topics (user_id);

create table Votes
(
    id        int auto_increment
        primary key,
    user_id   int                                   not null,
    topic_id  int                                   not null,
    vote_type enum ('up', 'down')                   not null,
    voted_at  timestamp default current_timestamp() not null,
    constraint Votes_ibfk_1
        foreign key (user_id) references Users (id),
    constraint Votes_ibfk_2
        foreign key (topic_id) references Topics (id)
);

create index topic_id
    on Votes (topic_id);

create index user_id
    on Votes (user_id);

