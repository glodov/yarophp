drop table tests;

create table tests (
	a int not null,
	b int not null,
	c varchar(10) not null comment 'just a comment',
	d datetime not null,
	
	primary key (a,b),
	index (c,d)
);


show full columns from tests;

show indexes from tests;

show create table tests;

show table status from riskstest like 'tests';