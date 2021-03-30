CREATE TABLE public."groups" (
	id bigserial NOT NULL,
	"name" varchar(120) NOT NULL,
	user_id int4 NULL,
	min_students_num int2 NOT NULL,
	max_students_num int2 NOT NULL,
	min_skills_num int2 NOT NULL,
	max_skills_num int2 NOT NULL,
    max_useless_skill_students float4 NULL,
	enabled bool NOT NULL,
	created int4 NOT NULL,
	CONSTRAINT groups_pk PRIMARY KEY (id)
);
CREATE UNIQUE INDEX groups_name_idx ON public.groups USING btree (name);

CREATE TABLE public.groups_skills (
	id bigserial NOT NULL,
	group_id int4 NOT NULL,
	skill_id int4 NOT NULL,
	CONSTRAINT groups_skills_pk PRIMARY KEY (id),
	CONSTRAINT groups_skills_fk FOREIGN KEY (group_id) REFERENCES groups(id),
	CONSTRAINT skills_fk FOREIGN KEY (skill_id) REFERENCES skills(id)
);
CREATE INDEX groups_skills_group_id_idx ON public.groups_skills USING btree (group_id, skill_id);

CREATE TABLE public.groups_users (
	id bigserial NOT NULL,
	group_id int4 NOT NULL,
	user_id int4 NOT NULL,
	CONSTRAINT groups_users_pk PRIMARY KEY (id),
	CONSTRAINT groups_users_fk FOREIGN KEY (user_id) REFERENCES users(id),
	CONSTRAINT users_fk FOREIGN KEY (group_id) REFERENCES groups(id)
);
CREATE INDEX groups_users_group_id_idx ON public.groups_users USING btree (group_id, user_id);

CREATE TABLE public.requests (
	id bigserial NOT NULL,
	status varchar(20) NOT NULL,
	created int4 NOT NULL,
	CONSTRAINT requests_pk PRIMARY KEY (id)
);

CREATE TABLE public.skills (
	id bigserial NOT NULL,
	"name" varchar(50) NOT NULL,
	CONSTRAINT skills_pk PRIMARY KEY (id)
);
CREATE UNIQUE INDEX skills_name_idx ON public.skills USING btree (name);

CREATE TABLE public.teachers_conditions (
	id bigserial NOT NULL,
	user_id int4 NOT NULL,
	max_groups_num int2 NOT NULL,
	min_group_size int2 NOT NULL,
	max_group_size int2 NOT NULL,
	CONSTRAINT teachers_conditions_pk PRIMARY KEY (id),
	CONSTRAINT teachers_conditions_fk FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE public.users (
	id bigserial NOT NULL,
	email varchar(80) NOT NULL,
	first_name varchar NOT NULL,
	last_name varchar NULL,
	phone varchar NULL,
	enabled bool NOT NULL,
	teacher bool NOT NULL,
	created int4 NOT NULL,
	CONSTRAINT users_pk PRIMARY KEY (id)
);
CREATE UNIQUE INDEX users_email_idx ON public.users USING btree (email);

CREATE TABLE public.users_skills (
	id bigserial NOT NULL,
	user_id int4 NOT NULL,
	skill_id int4 NOT NULL,
	CONSTRAINT users_skills_pk PRIMARY KEY (id),
	CONSTRAINT users_skills_fk FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE UNIQUE INDEX users_skills_user_id_idx ON public.users_skills USING btree (user_id, skill_id);
