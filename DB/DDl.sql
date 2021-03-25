CREATE TABLE public."groups" (
	id int4 NOT NULL,
	"name" text NOT NULL,
	user_id int4 NULL,
	min_pupil_num int2 NOT NULL,
	max_pupil_num int2 NOT NULL,
	min_skills_num int2 NOT NULL,
	max_skills_num int2 NOT NULL,
	max_useless_skill_pupil float4 NOT NULL,
	active bool NOT NULL,
	created int4 NOT NULL,
	CONSTRAINT groups_pk PRIMARY KEY (id),
	CONSTRAINT groups_fk FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE public.groups_skills (
	id int4 NOT NULL,
	group_id int4 NOT NULL,
	skill_id int4 NOT NULL,
	CONSTRAINT groups_skills_pk PRIMARY KEY (id),
	CONSTRAINT groups_skills_fk FOREIGN KEY (group_id) REFERENCES groups(id),
	CONSTRAINT groups_skills_fk_1 FOREIGN KEY (skill_id) REFERENCES skills(id)
);
CREATE UNIQUE INDEX groups_skills_group_id_idx ON public.groups_skills USING btree (group_id, skill_id);

CREATE TABLE public.groups_users (
	id int4 NOT NULL,
	group_id int4 NOT NULL,
	user_id int4 NOT NULL,
	CONSTRAINT groups_users_pk PRIMARY KEY (id),
	CONSTRAINT groups_users_fk FOREIGN KEY (group_id) REFERENCES groups(id),
	CONSTRAINT groups_users_fk_1 FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE INDEX groups_users_group_id_idx ON public.groups_users USING btree (group_id, user_id);

CREATE TABLE public.queue_statuses (
	id int2 NOT NULL,
	"name" varchar(20) NOT NULL,
	CONSTRAINT queue_statuses_pk PRIMARY KEY (id)
);

CREATE TABLE public.requests (
	id int4 NOT NULL,
	status_id int2 NOT NULL,
	created int4 NOT NULL,
	CONSTRAINT requests_pk PRIMARY KEY (id),
	CONSTRAINT requests_fk FOREIGN KEY (status_id) REFERENCES queue_statuses(id)
);

CREATE TABLE public.skills (
	id int4 NOT NULL,
	"name" varchar(50) NOT NULL,
	CONSTRAINT skills_pk PRIMARY KEY (id)
);
CREATE UNIQUE INDEX skills_name_idx ON public.skills USING btree (name);

CREATE TABLE public.teachers_conditions (
	id int4 NOT NULL,
	user_id int4 NOT NULL,
	max_groups_num int2 NOT NULL,
	min_group_size int2 NOT NULL,
	max_group_size int2 NOT NULL,
	CONSTRAINT teacher_conditions_pk PRIMARY KEY (id),
	CONSTRAINT teacher_conditions_fk FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE public.users (
	id int4 NOT NULL,
	email varchar(80) NOT NULL,
	first_name varchar NOT NULL,
	last_name varchar NULL,
	phone varchar NULL,
	status bool NOT NULL,
	teacher bool NOT NULL,
	created int4 NOT NULL,
	CONSTRAINT users_pk PRIMARY KEY (id)
);
CREATE UNIQUE INDEX users_email_idx ON public.users USING btree (email);

CREATE TABLE public.users_skills (
	id int4 NOT NULL,
	user_id int4 NULL,
	skill_id int4 NULL,
	CONSTRAINT users_skills_pk PRIMARY KEY (id),
	CONSTRAINT users_skills_fk FOREIGN KEY (skill_id) REFERENCES skills(id),
	CONSTRAINT users_skills_fk_1 FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE UNIQUE INDEX users_skills_user_id_idx ON public.users_skills USING btree (user_id, skill_id);



