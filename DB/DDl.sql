CREATE TABLE public.users
(
    id         bigserial   NOT NULL,
    email      varchar(80) NOT NULL,
    first_name varchar     NOT NULL,
    last_name  varchar     NULL,
    phone      varchar     NULL,
    enabled    bool        NOT NULL,
    teacher    bool        NOT NULL,
    updated_at timestamp   NOT NULL,
    created_at timestamp   NOT NULL,
    CONSTRAINT users_pk PRIMARY KEY (id)
);
CREATE UNIQUE INDEX users_email_idx ON public.users USING btree (email);

CREATE TABLE public."groups"
(
    id                         bigserial    NOT NULL,
    name                       varchar(120) NOT NULL,
    user_id                    int4         NULL,
    min_students_num           int2         NOT NULL,
    max_students_num           int2         NOT NULL,
    min_skills_num             int2         NOT NULL,
    max_skills_num             int2         NOT NULL,
    max_useless_skill_students float4       NULL,
    enabled                    bool         NOT NULL,
    created_at                 timestamp    NOT NULL,
    updated_at                 timestamp    NOT NULL,
    CONSTRAINT groups_pk PRIMARY KEY (id),
    CONSTRAINT groups_fk FOREIGN KEY (user_id) REFERENCES users (id)
);
CREATE UNIQUE INDEX groups_name_idx ON public.groups USING btree (name);

CREATE TABLE public.skills
(
    id         bigserial   NOT NULL,
    "name"     varchar(50) NOT NULL,
    updated_at timestamp        NOT NULL,
    created_at timestamp        NOT NULL,
    CONSTRAINT skills_pk PRIMARY KEY (id)
);
CREATE UNIQUE INDEX skills_name_idx ON public.skills USING btree (name);

CREATE TABLE public.requests
(
    id         bigserial   NOT NULL,
    status     varchar(20) NOT NULL,
    created_at timestamp        NOT NULL,
    updated_at timestamp        NOT NULL,
    CONSTRAINT requests_pk PRIMARY KEY (id)
);

CREATE TABLE public.groups_skills (
	id bigserial NOT NULL,
	group_id int4 NOT NULL,
	skill_id int4 NOT NULL,
	CONSTRAINT groups_skills_pk PRIMARY KEY (id),
	CONSTRAINT groups_fk FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
	CONSTRAINT skills_fk FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE
);
CREATE INDEX groups_skills_group_id_idx ON public.groups_skills USING btree (group_id, skill_id);;

CREATE TABLE public.groups_users (
	id bigserial NOT NULL,
	group_id int4 NOT NULL,
	user_id int4 NOT NULL,
	CONSTRAINT groups_users_pk PRIMARY KEY (id),
	CONSTRAINT groups_fk FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
	CONSTRAINT users_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE INDEX groups_users_group_id_idx ON public.groups_users USING btree (group_id, user_id);

CREATE TABLE public.teachers_conditions (
	id bigserial NOT NULL,
	user_id int4 NOT NULL,
	max_groups_num int2 NOT NULL,
	min_group_size int2 NOT NULL,
	max_group_size int2 NOT NULL,
	updated_at date NOT NULL,
	created_at date NOT NULL,
	CONSTRAINT teachers_conditions_pk PRIMARY KEY (id),
	CONSTRAINT users_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE UNIQUE INDEX teachers_conditions_user_id_idx ON public.teachers_conditions USING btree (user_id);

CREATE TABLE public.users_skills (
	id bigserial NOT NULL,
	user_id int4 NOT NULL,
	skill_id int4 NOT NULL,
	CONSTRAINT users_skills_pk PRIMARY KEY (id),
	CONSTRAINT skill_fk FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE,
	CONSTRAINT user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE UNIQUE INDEX users_skills_user_id_idx ON public.users_skills USING btree (user_id, skill_id);

ALTER TABLE public.teachers_conditions ADD CONSTRAINT teachers_conditions_check CHECK ((min_group_size <= max_group_size));
ALTER TABLE public."groups" ADD CONSTRAINT groups_skills_check CHECK ((min_skills_num <= max_skills_num));
ALTER TABLE public."groups" ADD CONSTRAINT groups_students_check CHECK ((min_students_num <= max_students_num));