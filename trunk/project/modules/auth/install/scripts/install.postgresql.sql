CREATE TABLE copixuser (
id_cusr             serial,
login_cusr          varchar(50) NOT NULL DEFAULT '',
password_cusr       varchar(50) NOT NULL DEFAULT '',
enabled_cusr        char(1) NOT NULL DEFAULT '1',
lostpassword_cusr   varchar(10) NOT NULL DEFAULT '',
email_cusr          varchar(255) NOT NULL DEFAULT '',
PRIMARY KEY(id_cusr)
);

INSERT INTO copixuser VALUES (1, 'admin',
'21232f297a57a5a743894a0e4a801fc3', 1, 'f11a8698ae',
'no@mail.com');