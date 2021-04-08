DROP TABLE IF EXISTS groups_skills;
DROP TABLE IF EXISTS groups_users;
DROP TABLE IF EXISTS groups;
DROP TABLE IF EXISTS requests;
DROP TABLE IF EXISTS users_skills;
DROP TABLE IF EXISTS teachers_conditions;
DROP TABLE IF EXISTS skills;
DROP TABLE IF EXISTS users;

--
-- PostgreSQL database dump
--

-- Dumped from database version 11.10 (Ubuntu 11.10-1.pgdg18.04+1)
-- Dumped by pg_dump version 11.10 (Ubuntu 11.10-1.pgdg18.04+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: groups; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.groups (
    id bigint NOT NULL,
    name character varying(120) NOT NULL,
    user_id integer,
    min_students_num smallint NOT NULL,
    max_students_num smallint NOT NULL,
    min_skills_num smallint NOT NULL,
    max_skills_num smallint NOT NULL,
    max_useless_skill_students real,
    enabled boolean NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL,
    CONSTRAINT groups_students_check CHECK ((min_students_num <= max_students_num))
);


ALTER TABLE public.groups OWNER TO postgres;

--
-- Name: groups_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.groups_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.groups_id_seq OWNER TO postgres;

--
-- Name: groups_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.groups_id_seq OWNED BY public.groups.id;


--
-- Name: groups_skills; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.groups_skills (
    id bigint NOT NULL,
    group_id integer NOT NULL,
    skill_id integer NOT NULL
);


ALTER TABLE public.groups_skills OWNER TO postgres;

--
-- Name: groups_skills_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.groups_skills_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.groups_skills_id_seq OWNER TO postgres;

--
-- Name: groups_skills_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.groups_skills_id_seq OWNED BY public.groups_skills.id;


--
-- Name: groups_users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.groups_users (
    id bigint NOT NULL,
    group_id integer NOT NULL,
    user_id integer NOT NULL
);


ALTER TABLE public.groups_users OWNER TO postgres;

--
-- Name: groups_users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.groups_users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.groups_users_id_seq OWNER TO postgres;

--
-- Name: groups_users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.groups_users_id_seq OWNED BY public.groups_users.id;


--
-- Name: requests; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.requests (
    id bigint NOT NULL,
    status character varying(20) NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL
);


ALTER TABLE public.requests OWNER TO postgres;

--
-- Name: requests_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.requests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.requests_id_seq OWNER TO postgres;

--
-- Name: requests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.requests_id_seq OWNED BY public.requests.id;


--
-- Name: skills; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.skills (
    id bigint NOT NULL,
    name character varying(50) NOT NULL,
    updated_at timestamp without time zone NOT NULL,
    created_at timestamp without time zone NOT NULL
);


ALTER TABLE public.skills OWNER TO postgres;

--
-- Name: skills_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.skills_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.skills_id_seq OWNER TO postgres;

--
-- Name: skills_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.skills_id_seq OWNED BY public.skills.id;


--
-- Name: teachers_conditions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.teachers_conditions (
    id bigint NOT NULL,
    user_id integer NOT NULL,
    max_groups_num smallint NOT NULL,
    min_group_size smallint NOT NULL,
    max_group_size smallint NOT NULL,
    updated_at timestamp without time zone NOT NULL,
    created_at timestamp without time zone NOT NULL
);


ALTER TABLE public.teachers_conditions OWNER TO postgres;

--
-- Name: teachers_conditions_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.teachers_conditions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.teachers_conditions_id_seq OWNER TO postgres;

--
-- Name: teachers_conditions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.teachers_conditions_id_seq OWNED BY public.teachers_conditions.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    email character varying(80) NOT NULL,
    first_name character varying NOT NULL,
    last_name character varying,
    phone character varying,
    enabled boolean NOT NULL,
    teacher boolean NOT NULL,
    updated_at timestamp without time zone NOT NULL,
    created_at timestamp without time zone NOT NULL
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: users_skills; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users_skills (
    id bigint NOT NULL,
    user_id integer NOT NULL,
    skill_id integer NOT NULL
);


ALTER TABLE public.users_skills OWNER TO postgres;

--
-- Name: users_skills_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_skills_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_skills_id_seq OWNER TO postgres;

--
-- Name: users_skills_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_skills_id_seq OWNED BY public.users_skills.id;


--
-- Name: groups id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.groups ALTER COLUMN id SET DEFAULT nextval('public.groups_id_seq'::regclass);


--
-- Name: groups_skills id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.groups_skills ALTER COLUMN id SET DEFAULT nextval('public.groups_skills_id_seq'::regclass);


--
-- Name: groups_users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.groups_users ALTER COLUMN id SET DEFAULT nextval('public.groups_users_id_seq'::regclass);


--
-- Name: requests id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.requests ALTER COLUMN id SET DEFAULT nextval('public.requests_id_seq'::regclass);


--
-- Name: skills id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.skills ALTER COLUMN id SET DEFAULT nextval('public.skills_id_seq'::regclass);


--
-- Name: teachers_conditions id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.teachers_conditions ALTER COLUMN id SET DEFAULT nextval('public.teachers_conditions_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: users_skills id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users_skills ALTER COLUMN id SET DEFAULT nextval('public.users_skills_id_seq'::regclass);


--
-- Data for Name: groups; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.groups (id, name, user_id, min_students_num, max_students_num, min_skills_num, max_skills_num, max_useless_skill_students, enabled, created_at, updated_at) FROM stdin;
1	recusandae eveniet repudiandae	\N	5	10	2	8	1.5	t	2021-04-07 20:00:27	2021-04-07 20:00:27
3	rem qui ut	\N	5	10	2	8	1.5	f	2021-04-07 20:06:10	2021-04-07 20:06:10
4	qui et deserunt	\N	5	10	2	8	1.5	f	2021-04-07 20:06:10	2021-04-07 20:06:10
6	autem corporis eaque	\N	1	2	5	10	1	f	2021-04-07 20:07:10	2021-04-07 20:07:10
7	recusandae voluptatem iste	\N	5	8	2	8	1	f	2021-04-07 20:07:49	2021-04-07 20:07:49
12	exercitationem est aliquam	\N	4	10	5	8	1	f	2021-04-07 20:12:01	2021-04-07 20:12:01
13	cum veniam qui	\N	4	10	5	8	1	f	2021-04-07 20:12:04	2021-04-07 20:12:04
14	et animi omnis	\N	4	10	2	4	1	f	2021-04-07 20:12:26	2021-04-07 20:12:26
16	non ut sint	\N	4	10	2	8	1	f	2021-04-07 20:13:02	2021-04-07 20:13:02
17	ab qui aperiam	\N	5	10	2	8	1	f	2021-04-07 20:13:15	2021-04-07 20:13:15
20	aut debitis sed	72	4	9	5	6	1	t	2021-04-07 20:14:40	2021-04-07 23:04:02
10	excepturi dolores tempore	57	8	10	2	8	1	t	2021-04-07 20:11:34	2021-04-07 23:05:59
2	nihil veritatis aut	\N	4	9	5	6	1	f	2021-04-07 20:06:08	2021-04-08 08:52:18
15	inventore rerum dicta	\N	4	9	5	6	1	f	2021-04-07 20:12:50	2021-04-08 09:33:40
5	amet eligendi omnis	\N	4	9	5	6	1	f	2021-04-07 20:06:11	2021-04-08 09:33:55
21	maiores aut illo	\N	4	9	5	6	1	f	2021-04-08 10:08:48	2021-04-08 10:08:48
11	id sed ipsa	\N	4	9	5	6	1	f	2021-04-07 20:11:56	2021-04-08 10:09:28
\.


--
-- Data for Name: groups_skills; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.groups_skills (id, group_id, skill_id) FROM stdin;
7	3	5
8	3	6
9	3	7
10	3	8
11	3	9
12	3	12
13	4	5
14	4	6
15	4	7
16	4	8
17	4	9
18	4	12
25	6	2
26	6	4
27	6	5
28	6	6
29	6	7
30	6	8
31	6	9
32	6	10
33	6	11
34	6	12
35	7	4
36	7	8
37	7	9
38	7	20
39	7	21
40	10	9
41	10	17
42	10	18
43	10	20
44	10	21
50	12	4
51	12	5
52	12	6
53	12	7
54	12	8
55	13	4
56	13	5
57	13	6
58	13	7
59	13	8
60	13	10
61	14	10
62	14	11
63	14	12
67	16	15
68	16	16
69	16	17
70	16	18
71	16	19
72	17	2
73	17	3
74	17	4
75	17	5
76	17	6
77	17	7
90	20	3
91	20	6
92	20	7
93	20	8
94	20	9
95	20	13
96	20	16
98	2	3
99	2	4
100	2	5
101	2	7
102	15	3
103	5	3
104	11	3
105	21	3
106	21	6
107	21	7
108	21	13
109	21	16
110	21	4
111	21	8
112	21	19
113	21	20
114	11	4
115	11	5
116	11	6
117	11	7
118	11	8
119	11	9
\.


--
-- Data for Name: groups_users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.groups_users (id, group_id, user_id) FROM stdin;
10	2	5
\.


--
-- Data for Name: requests; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.requests (id, status, created_at, updated_at) FROM stdin;
1	open	2021-04-07 20:38:06	2021-04-07 20:38:06
2	open	2021-04-07 20:40:07	2021-04-07 20:40:07
3	open	2021-04-07 20:42:10	2021-04-07 20:42:10
4	open	2021-04-07 20:43:55	2021-04-07 20:43:55
5	open	2021-04-07 20:45:46	2021-04-07 20:45:46
6	open	2021-04-07 20:45:47	2021-04-07 20:45:47
7	open	2021-04-07 20:45:48	2021-04-07 20:45:48
8	completed	2021-04-07 22:16:23	2021-04-07 22:16:33
9	completed	2021-04-07 22:17:20	2021-04-07 22:17:20
10	open	2021-04-07 22:49:05	2021-04-07 22:49:05
11	open	2021-04-07 22:51:08	2021-04-07 22:51:08
12	open	2021-04-07 22:52:37	2021-04-07 22:52:37
13	open	2021-04-07 22:54:00	2021-04-07 22:54:00
14	open	2021-04-07 22:56:56	2021-04-07 22:56:56
15	open	2021-04-07 22:58:33	2021-04-07 22:58:33
16	open	2021-04-07 23:00:27	2021-04-07 23:00:27
17	open	2021-04-07 23:00:42	2021-04-07 23:00:42
18	completed	2021-04-07 23:03:58	2021-04-07 23:04:02
19	completed	2021-04-07 23:04:00	2021-04-07 23:04:02
20	completed	2021-04-07 23:04:33	2021-04-07 23:04:35
21	completed	2021-04-07 23:05:39	2021-04-07 23:05:39
22	completed	2021-04-07 23:05:59	2021-04-07 23:05:59
23	completed	2021-04-08 07:07:56	2021-04-08 07:27:32
24	completed	2021-04-08 07:08:25	2021-04-08 07:27:32
25	completed	2021-04-08 07:28:01	2021-04-08 07:28:01
\.


--
-- Data for Name: skills; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.skills (id, name, updated_at, created_at) FROM stdin;
2	PHP	2021-03-10 00:00:00	2021-03-10 00:00:00
3	MySQL	2021-03-10 00:00:00	2021-03-10 00:00:00
4	PostgreSQL	2021-03-10 00:00:00	2021-03-10 00:00:00
5	SOLID	2021-03-10 00:00:00	2021-03-10 00:00:00
7	ClickHouse	2021-03-10 00:00:00	2021-03-10 00:00:00
6	Redis	2021-03-10 00:00:00	2021-03-10 00:00:00
8	MongoDB	2021-03-10 00:00:00	2021-03-10 00:00:00
9	Elasticsearch	2021-03-10 00:00:00	2021-03-10 00:00:00
10	Git	2021-03-10 00:00:00	2021-03-10 00:00:00
11	PHPUnit	2021-03-10 00:00:00	2021-03-10 00:00:00
12	RabbitMQ	2021-03-10 00:00:00	2021-03-10 00:00:00
13	REST API	2021-03-10 00:00:00	2021-03-10 00:00:00
14	Laravel	2021-03-10 00:00:00	2021-03-10 00:00:00
15	Symfony	2021-03-10 00:00:00	2021-03-10 00:00:00
16	Node.js	2021-03-10 00:00:00	2021-03-10 00:00:00
17	JavaScript	2021-03-10 00:00:00	2021-03-10 00:00:00
18	AJAX	2021-03-10 00:00:00	2021-03-10 00:00:00
19	JAVA	2021-03-10 00:00:00	2021-03-10 00:00:00
20	AngularJS	2021-03-10 00:00:00	2021-03-10 00:00:00
21	Vue.js	2021-03-10 00:00:00	2021-03-10 00:00:00
22	Express.js	2021-03-10 00:00:00	2021-03-10 00:00:00
\.


--
-- Data for Name: teachers_conditions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.teachers_conditions (id, user_id, max_groups_num, min_group_size, max_group_size, updated_at, created_at) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, email, first_name, last_name, phone, enabled, teacher, updated_at, created_at) FROM stdin;
3	Gretchen.Mills38@yahoo.com	Katheryn60	Elwin_Hilpert37	708-860-8092	t	f	2021-04-07 18:08:27	2021-04-07 18:08:27
4	Raquel_Okuneva70@yahoo.com	Therese_McLaughlin	Hobart.Waters52	381-517-8554	t	f	2021-04-07 18:15:24	2021-04-07 18:15:24
5	Christy62@hotmail.com	Chaya82	Montana.Macejkovic19	209-226-7836	t	f	2021-04-07 18:15:40	2021-04-07 18:15:40
6	Chyna_Jerde13@hotmail.com	Troy85	Stephon_Dare	657-439-8512	t	f	2021-04-07 18:16:30	2021-04-07 18:16:30
7	Alverta_Wuckert59@hotmail.com	Abraham_Streich	Katelyn10	648-204-1240	t	f	2021-04-07 18:16:42	2021-04-07 18:16:42
8	Dean.Bradtke4@yahoo.com	Joseph.Blanda	Eulalia_West43	752-752-1203	t	f	2021-04-07 18:17:02	2021-04-07 18:17:02
9	Valentine0@gmail.com	Melyna91	Ismael54	822-412-4014	t	f	2021-04-07 18:17:14	2021-04-07 18:17:14
10	Rogelio62@gmail.com	Keara26	Hulda.Mueller46	337-755-4954	t	f	2021-04-07 18:17:26	2021-04-07 18:17:26
11	Callie_Schimmel@hotmail.com	Jimmie_Feest	Carolyne.Mueller	534-795-0500	t	f	2021-04-07 18:17:41	2021-04-07 18:17:41
12	Maegan.Larson@gmail.com	Addison92	Mathilde_Goyette	245-540-3123	t	f	2021-04-07 18:17:48	2021-04-07 18:17:48
13	Hannah83@yahoo.com	Demarco97	Haley16	752-228-2632	t	f	2021-04-07 18:18:11	2021-04-07 18:18:11
14	Erin77@gmail.com	Aryanna_Ziemann	May94	877-917-4450	t	f	2021-04-07 18:18:31	2021-04-07 18:18:31
15	Scottie.Schroeder21@hotmail.com	Kaitlin_Lang86	Khalil.Ullrich	214-879-2236	t	f	2021-04-07 18:18:45	2021-04-07 18:18:45
16	Jessy.Boyer51@gmail.com	Kaela37	Emilie.Boehm57	690-659-7838	t	f	2021-04-07 18:19:06	2021-04-07 18:19:06
17	Garth_Schaefer@gmail.com	Skyla5	Asa.Langosh	268-743-8560	t	f	2021-04-07 18:19:48	2021-04-07 18:19:48
18	Colby27@hotmail.com	Noemi23	Raphaelle.Hand26	414-446-2457	t	f	2021-04-07 18:19:56	2021-04-07 18:19:56
19	Loy58@gmail.com	Chaya_Gleason84	Eula52	956-584-0380	t	f	2021-04-07 18:20:19	2021-04-07 18:20:19
20	Syble_Schmitt@yahoo.com	Ignacio_Gleichner79	Rudy76	723-595-2309	t	f	2021-04-07 18:20:40	2021-04-07 18:20:40
21	Johnson91@yahoo.com	Nelle.Wisozk83	Dillan.Stoltenberg	629-911-5403	t	f	2021-04-07 18:20:44	2021-04-07 18:20:44
22	Adaline.Towne60@yahoo.com	Estevan_OReilly88	Shyanne.Willms4	605-869-0738	t	f	2021-04-07 18:20:53	2021-04-07 18:20:53
23	Aubree95@gmail.com	Woodrow.Dickens20	Francisco.Mayer33	670-936-9764	t	f	2021-04-07 18:21:29	2021-04-07 18:21:29
24	Pablo14@yahoo.com	Brandyn_Goyette68	Seamus45	337-961-9234	t	f	2021-04-07 18:21:39	2021-04-07 18:21:39
25	Wilber.Swift@yahoo.com	Destin57	Marquise98	913-374-6488	t	f	2021-04-07 18:21:57	2021-04-07 18:21:57
26	Joaquin7@yahoo.com	Patsy.Smitham75	Asia_Quitzon	473-880-5499	t	f	2021-04-07 18:22:02	2021-04-07 18:22:02
27	Dean.Bogisich8@gmail.com	Jasen_Nicolas	Elfrieda_Blanda	855-312-1885	t	f	2021-04-07 18:22:06	2021-04-07 18:22:06
28	Ubaldo_Keeling95@yahoo.com	Cedrick_Murphy63	Christa_Volkman2	239-265-3280	t	f	2021-04-07 18:22:10	2021-04-07 18:22:10
29	Carissa.Langworth@hotmail.com	Lesly10	Devin_Konopelski	933-761-7156	t	f	2021-04-07 18:22:13	2021-04-07 18:22:13
31	Verdie_Dietrich89@gmail.com	Roman64	Colin.Wunsch6	381-200-6146	t	f	2021-04-07 18:22:24	2021-04-07 18:22:24
32	Audrey_Nader21@gmail.com	Maximilian.Rowe	Betty_Kunze	968-268-4794	t	f	2021-04-07 18:22:42	2021-04-07 18:22:42
33	Amelia_Rowe9@gmail.com	Kali_Ratke	Jedidiah23	359-690-0268	t	f	2021-04-07 18:22:59	2021-04-07 18:22:59
34	Hilda86@gmail.com	Judge_Watsica	Stanton33	647-661-1369	t	f	2021-04-07 19:17:32	2021-04-07 19:17:32
35	Tomasa_Gutmann47@gmail.com	Julia_Witting73	Arne_Bogan	836-715-5852	t	f	2021-04-07 19:17:37	2021-04-07 19:17:37
36	Ida_Walter@hotmail.com	Bernard.Hoppe	Gregoria_Schaden45	835-652-0363	t	f	2021-04-07 19:17:46	2021-04-07 19:17:46
37	Nola.Denesik@gmail.com	Camron.Lesch	Van_Ernser44	645-376-3100	t	f	2021-04-07 19:17:48	2021-04-07 19:17:48
38	Osbaldo67@yahoo.com	Palma.Gottlieb71	Maximillian_Beahan98	768-967-3367	t	f	2021-04-07 19:17:53	2021-04-07 19:17:53
39	Alexandria.Feeney12@hotmail.com	Jazmyn_Tillman	Lolita14	458-547-7734	t	f	2021-04-07 19:18:01	2021-04-07 19:18:01
40	Cary.Rippin@hotmail.com	Alejandrin26	Fatima.Jacobi2	769-450-8652	t	f	2021-04-07 19:18:06	2021-04-07 19:18:06
41	Nathen27@hotmail.com	Jaeden72	Trenton.Will	279-697-3309	t	f	2021-04-07 19:18:10	2021-04-07 19:18:10
42	Cydney60@gmail.com	Shakira81	Frederique.Rutherford63	263-261-9987	t	f	2021-04-07 19:18:20	2021-04-07 19:18:20
43	Davon_Corkery12@hotmail.com	Melyna.Johnston	Tyra.Bauch	205-597-8330	t	f	2021-04-07 19:18:34	2021-04-07 19:18:34
44	Monica.Koch@yahoo.com	Jaime_Okuneva65	Chet_Hartmann7	781-600-7378	t	f	2021-04-07 19:18:38	2021-04-07 19:18:38
46	Janie.Lubowitz49@gmail.com	Elza_Langworth	Hilario.Dietrich	474-890-5917	t	f	2021-04-07 19:19:18	2021-04-07 19:19:18
47	Enrico_Cummings12@yahoo.com	Esta.Steuber	Sigurd_Lockman37	844-436-6149	t	f	2021-04-07 19:19:22	2021-04-07 19:19:22
48	Jackeline.Hand@hotmail.com	Orrin.Bruen	Holden_Padberg99	324-589-3179	t	f	2021-04-07 19:19:25	2021-04-07 19:19:25
49	Maiya.Walter72@hotmail.com	Reyna3	Mireille.Franecki	274-431-5912	t	f	2021-04-07 19:19:27	2021-04-07 19:19:27
51	Claud19@gmail.com	Ona_Mante52	Miguel_Stoltenberg	268-853-4345	t	f	2021-04-07 19:19:34	2021-04-07 19:19:34
52	Heath.Gibson44@yahoo.com	Berneice.Murray	Deborah36	816-277-0789	t	f	2021-04-07 19:19:52	2021-04-07 19:19:52
53	Lazaro7@yahoo.com	Marisa_Lowe	Macie_Parker	644-621-5607	t	f	2021-04-07 19:19:56	2021-04-07 19:19:56
54	Albin_Heathcote25@yahoo.com	Richie_Tremblay74	Owen_Wilderman69	727-745-1875	t	f	2021-04-07 19:20:00	2021-04-07 19:20:00
55	Erick_Schoen@hotmail.com	Jacques85	Elnora_Runte	358-745-8370	t	f	2021-04-07 19:20:04	2021-04-07 19:20:04
56	Donnie_Nader@yahoo.com	Margarita.OKeefe54	Clotilde83	597-728-8424	t	f	2021-04-07 19:20:13	2021-04-07 19:20:13
58	Patsy_Moen30@yahoo.com	Tatyana.Kuhn42	Lucy53	469-363-0259	t	f	2021-04-07 19:20:19	2021-04-07 19:20:19
59	Mario72@hotmail.com	Ettie.Stracke	Warren.Tillman	432-338-7713	t	f	2021-04-07 19:20:26	2021-04-07 19:20:26
60	Santos27@hotmail.com	Jo73	Shad_Frami	896-412-1365	t	f	2021-04-07 19:20:29	2021-04-07 19:20:29
62	Xavier.Kreiger76@gmail.com	Braeden.Lang	Rozella43	792-989-4795	t	f	2021-04-07 19:20:37	2021-04-07 19:20:37
63	Lydia_McDermott@gmail.com	Agustina_Hermiston76	Ariane.Lindgren	843-794-0226	t	f	2021-04-07 19:20:53	2021-04-07 19:20:53
64	Amparo80@gmail.com	Antone_Streich13	Rey_Luettgen95	721-575-8081	t	f	2021-04-07 19:21:01	2021-04-07 19:21:01
66	Margaretta30@yahoo.com	Paris.Boehm	Nayeli_Hoppe	620-438-1741	t	f	2021-04-07 19:21:11	2021-04-07 19:21:11
67	Edmund.Collins@hotmail.com	Rosina84	Ike32	530-812-3244	t	f	2021-04-07 19:21:14	2021-04-07 19:21:14
45	Tyshawn.Stamm@yahoo.com	Alphonso_Ryan	Casper_OHara	318-439-7442	f	f	2021-04-07 19:37:53	2021-04-07 19:18:43
57	Hilda23@hotmail.com	Providenci37	Breana77	609-328-2972	t	t	2021-04-07 19:20:17	2021-04-07 19:20:17
61	Isidro.Mueller@yahoo.com	Greyson.Boyle72	Marshall_Sporer	228-923-8206	t	t	2021-04-07 19:20:33	2021-04-07 19:20:33
65	Lindsay52@yahoo.com	Felicity.Goodwin	Jimmie.Cummings62	322-250-9249	t	t	2021-04-07 19:21:03	2021-04-07 19:21:03
69	Ignatius58@gmail.com	Myra29	Jerald_Pfeffer	938-292-5877	t	f	2021-04-07 19:21:32	2021-04-07 19:21:32
71	Esteban83@hotmail.com	Vallie.Kirlin	Brent.Gerlach	816-699-1292	t	f	2021-04-07 19:21:46	2021-04-07 19:21:46
73	Sadie_Ledner@yahoo.com	Dale.Kozey63	Raoul.Leuschke26	609-610-6719	t	f	2021-04-07 19:21:57	2021-04-07 19:21:57
74	Giovanni.Littel58@gmail.com	Elmo63	Tressie93	251-810-6389	t	f	2021-04-07 19:22:00	2021-04-07 19:22:00
76	Cathryn94@gmail.com	Richard.Littel	Helena_Buckridge48	734-420-4019	t	f	2021-04-07 19:22:08	2021-04-07 19:22:08
77	Samantha_Schumm87@hotmail.com	Norberto36	Domenic.Abshire24	621-801-8818	t	f	2021-04-07 19:22:11	2021-04-07 19:22:11
78	Mack.Cronin@hotmail.com	Gabriella_Zulauf30	Crystal.McKenzie3	765-830-7231	t	f	2021-04-07 19:22:14	2021-04-07 19:22:14
79	Giovanny33@hotmail.com	Kade61	Selena.Volkman57	443-682-4614	t	f	2021-04-07 19:22:22	2021-04-07 19:22:22
80	Wyatt57@yahoo.com	Miles.Stark15	Lambert48	255-457-9558	t	f	2021-04-07 19:22:26	2021-04-07 19:22:26
81	Billie7@yahoo.com	Christy_Jaskolski27	Kendra4	325-644-3110	t	f	2021-04-07 19:22:49	2021-04-07 19:22:49
50	Teagan_Cronin@hotmail.com	Lauren_Considine41	Meredith91	375-304-4187	f	f	2021-04-07 19:37:40	2021-04-07 19:19:30
68	Elisabeth_Kihn@yahoo.com	Dwight_Wilderman	Lisette.Predovic17	680-894-8279	t	t	2021-04-07 19:21:27	2021-04-07 19:21:27
72	Dangelo82@hotmail.com	Herminio.Kertzmann	Ericka66	337-449-8141	t	t	2021-04-07 19:21:53	2021-04-07 19:21:53
75	Brook_Schinner55@gmail.com	Maida_Mohr	Aimee1	994-386-6203	t	t	2021-04-07 19:22:02	2021-04-07 19:22:02
82	Crystal91@hotmail.com	Holden.Kunde	Jadon66	682-211-0087	t	f	2021-04-08 10:29:56	2021-04-08 10:29:56
83	Lelia_Braun@hotmail.com	Noe_Greenholt58	Savanna.Bergstrom83	476-457-0824	t	f	2021-04-08 10:47:21	2021-04-08 10:47:21
84	Darrin2@yahoo.com	Christian71	Scottie.Miller	672-889-7295	t	f	2021-04-08 10:49:23	2021-04-08 10:49:23
85	Lorna.Purdy@yahoo.com	Justyn89	Rowan_Hirthe	515-376-7598	t	f	2021-04-08 10:50:15	2021-04-08 10:50:15
\.


--
-- Data for Name: users_skills; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users_skills (id, user_id, skill_id) FROM stdin;
3	3	2
4	3	4
5	3	5
6	4	22
7	4	15
8	4	5
9	5	22
10	5	15
11	5	5
12	5	6
13	5	7
14	5	9
15	6	10
16	6	11
17	6	12
18	6	13
19	6	14
20	6	15
21	6	16
22	7	2
23	7	3
24	7	4
25	7	5
26	7	6
27	7	7
28	7	8
29	7	9
30	7	10
31	8	11
32	8	12
33	8	13
34	8	14
35	8	15
36	8	16
37	8	17
38	8	18
39	8	19
40	9	11
41	9	13
42	9	14
43	9	16
44	9	17
45	9	19
46	10	11
47	10	13
48	10	14
49	10	16
50	10	17
51	10	19
52	10	2
53	10	5
54	10	8
55	11	11
56	11	14
57	11	16
58	11	17
59	11	5
60	11	8
61	12	11
62	12	17
63	12	5
64	12	8
65	13	22
66	13	21
67	13	20
68	13	19
69	13	18
70	13	17
71	14	22
72	14	2
73	14	5
74	14	10
75	14	17
76	14	16
77	14	18
78	14	19
79	15	22
80	15	2
81	15	5
82	15	10
83	15	17
84	15	16
85	15	18
86	15	19
87	15	6
88	15	7
89	15	8
90	15	9
91	16	22
92	16	11
93	16	2
94	16	14
95	16	6
96	16	18
97	16	20
98	16	7
99	17	22
100	17	14
101	17	6
102	17	18
103	17	20
104	17	7
105	17	11
106	17	12
107	17	13
108	17	15
109	17	16
110	18	22
111	18	14
112	18	6
113	18	13
114	18	15
115	18	16
116	19	2
117	19	4
118	19	6
119	19	8
120	19	10
121	19	12
122	19	14
123	19	16
124	19	18
125	19	20
126	20	3
127	20	5
128	20	7
129	20	9
130	20	11
131	20	13
132	20	15
133	20	17
134	20	19
135	20	21
136	20	22
137	21	3
138	21	9
139	21	11
140	21	13
141	21	15
142	21	17
143	21	19
144	21	21
145	21	22
146	22	3
147	22	9
148	22	11
149	22	13
150	22	15
151	22	17
152	22	2
153	23	3
154	23	6
155	23	9
156	23	12
157	23	15
158	23	18
159	24	3
160	24	6
161	24	9
162	24	4
163	24	22
164	24	12
165	24	15
166	24	18
167	25	3
168	25	6
169	25	9
170	25	4
171	25	22
172	25	18
173	26	3
174	26	6
175	26	9
176	26	4
177	26	22
178	26	18
179	26	2
180	27	3
181	27	6
182	27	9
183	27	4
184	27	22
185	27	18
186	27	2
187	28	3
188	28	6
189	28	9
190	28	4
191	28	22
192	28	18
193	28	2
194	28	5
195	29	3
196	29	6
197	29	9
198	29	4
199	29	22
200	29	18
201	29	2
202	29	5
203	29	7
214	31	3
215	31	6
216	31	9
217	31	4
218	31	22
219	31	18
220	31	2
221	31	5
222	31	7
223	31	11
224	31	13
225	31	12
226	31	15
227	32	10
228	32	11
229	32	12
230	32	13
231	32	14
232	32	15
233	32	16
234	32	17
235	32	18
236	32	19
237	32	20
238	32	21
239	32	22
240	33	10
241	33	12
242	33	13
243	33	15
244	33	16
245	33	18
246	33	19
247	33	21
248	33	22
249	34	2
250	34	10
251	34	11
252	34	12
253	34	14
254	34	15
255	34	17
256	35	2
257	35	10
258	35	11
259	35	15
260	35	17
261	36	2
262	36	10
263	36	11
264	36	15
265	36	17
266	36	4
267	36	5
268	36	6
269	36	8
270	37	2
271	37	10
272	37	11
273	37	15
274	37	17
275	37	4
276	37	5
277	37	6
278	37	8
279	38	2
280	38	10
281	38	11
282	38	15
283	38	5
284	38	6
285	38	8
286	39	2
287	39	10
288	39	11
289	39	15
290	39	5
291	39	6
292	39	9
293	39	18
294	39	16
295	40	2
296	40	10
297	40	11
298	40	15
299	40	5
300	40	6
301	40	9
302	40	18
303	40	12
304	41	2
305	41	10
306	41	11
307	41	15
308	41	5
309	41	6
310	41	9
311	41	18
312	41	13
313	42	2
314	42	10
315	42	11
316	42	15
317	42	5
318	42	6
319	42	9
320	42	18
321	42	8
322	42	7
323	42	4
324	43	2
325	43	3
326	43	4
327	43	5
328	43	6
329	44	2
330	44	3
331	44	4
332	44	5
333	44	6
334	44	7
338	45	5
344	46	2
345	46	3
346	46	4
347	46	5
348	46	6
349	46	7
350	46	8
351	46	9
352	46	10
353	46	12
354	46	13
355	46	11
356	46	15
357	46	18
358	47	2
359	47	3
360	47	4
361	47	5
362	47	6
363	47	7
364	47	8
365	47	9
366	47	10
367	47	12
368	47	13
369	47	11
370	47	15
371	48	2
372	48	3
373	48	4
374	48	5
375	48	6
376	48	7
377	48	8
378	48	9
379	48	10
380	48	12
381	48	13
382	48	11
383	49	2
384	49	3
385	49	4
386	49	5
387	49	6
388	49	7
389	49	8
390	49	9
391	49	10
392	49	12
393	49	13
394	50	2
397	50	5
398	50	6
399	50	7
400	50	8
401	50	9
403	50	12
404	51	5
405	51	6
406	51	7
407	51	8
408	51	9
409	51	10
410	51	12
411	52	5
412	52	6
413	52	7
414	52	8
415	52	9
416	52	10
417	52	12
418	52	20
419	52	21
420	52	22
421	52	19
422	53	5
423	53	6
424	53	7
425	53	8
426	53	9
427	53	10
428	53	12
429	53	20
430	53	21
431	53	22
432	53	19
433	53	18
434	53	17
435	54	5
436	54	6
437	54	7
438	54	8
439	54	9
440	54	10
441	54	12
442	54	20
443	54	21
444	54	22
445	54	19
446	54	18
447	54	17
448	54	16
449	54	15
450	55	5
451	55	6
452	55	7
453	55	8
454	55	9
455	55	10
456	55	12
457	55	20
458	55	21
459	55	22
460	55	19
461	55	18
462	55	17
463	55	16
464	55	15
465	55	14
466	56	5
467	56	6
468	56	7
469	56	8
470	56	9
471	56	10
472	56	12
473	56	20
474	56	21
475	56	22
476	56	19
477	56	18
478	56	17
479	56	16
480	57	5
481	57	6
482	57	7
483	57	8
484	57	9
485	57	10
486	57	12
487	57	20
488	57	21
489	57	22
490	57	19
491	57	18
492	57	17
493	58	5
494	58	6
495	58	7
496	58	8
497	58	9
498	58	10
499	58	12
500	58	20
501	58	21
502	58	22
503	58	19
504	58	18
505	59	5
506	59	6
507	59	7
508	59	8
509	59	9
510	59	12
511	59	20
512	59	21
513	59	22
514	59	19
515	59	18
516	60	5
517	60	6
518	60	7
519	60	8
520	60	9
521	60	12
522	60	20
523	60	21
524	60	19
525	60	18
526	61	5
527	61	6
528	61	7
529	61	8
530	61	9
531	61	12
532	61	20
533	61	19
534	61	18
535	62	5
536	62	6
537	62	7
538	62	8
539	62	9
540	62	12
541	62	20
542	62	19
543	63	5
544	63	6
545	63	7
546	63	8
547	63	9
548	63	12
549	63	20
550	63	19
551	63	2
552	63	3
553	63	4
554	64	5
555	64	6
556	64	7
557	64	8
558	64	9
559	64	12
560	64	20
561	64	19
562	64	2
563	64	3
564	64	4
565	65	5
566	65	6
567	65	7
568	65	8
569	65	9
570	65	12
571	65	20
572	65	19
573	65	2
574	65	3
575	65	4
576	66	5
577	66	6
578	66	7
579	66	8
580	66	9
581	66	12
582	66	20
583	66	19
584	66	2
585	66	3
586	66	4
587	67	5
588	67	6
589	67	7
590	67	8
591	67	9
592	67	12
593	67	20
594	67	19
595	67	2
596	67	3
597	68	5
598	68	6
599	68	7
600	68	8
601	68	9
602	68	12
603	68	20
604	68	19
605	68	2
606	68	3
607	68	18
608	68	17
609	69	5
610	69	6
611	69	7
612	69	8
613	69	9
614	69	12
615	69	20
616	69	19
617	69	2
618	69	3
619	69	18
620	69	17
621	69	16
637	71	5
638	71	6
639	71	7
640	71	8
641	71	9
642	71	12
643	71	20
644	71	19
645	71	2
646	71	3
647	71	18
648	71	17
649	71	16
650	71	15
651	71	14
652	72	5
653	72	6
654	72	7
655	72	8
656	72	9
657	72	12
658	72	20
659	72	19
660	72	2
661	72	3
662	72	18
663	72	17
664	72	16
665	72	15
666	72	14
667	72	13
668	73	5
669	73	6
670	73	7
671	73	8
672	73	9
673	73	12
674	73	20
675	73	19
676	73	2
677	73	3
678	73	18
679	73	17
680	73	16
681	73	15
682	73	14
683	73	13
684	74	5
685	74	6
686	74	7
687	74	8
688	74	9
689	74	12
690	74	20
691	74	19
692	74	2
693	74	3
694	74	18
695	74	17
696	74	16
697	74	15
698	74	14
699	75	5
700	75	6
701	75	7
702	75	8
703	75	9
704	75	12
705	75	20
706	75	19
707	75	2
708	75	3
709	75	18
710	75	17
711	75	16
712	75	15
713	76	5
714	76	6
715	76	7
716	76	8
717	76	9
718	76	12
719	76	20
720	76	19
721	76	2
722	76	3
723	76	18
724	76	17
725	76	16
726	77	5
727	77	6
728	77	7
729	77	8
730	77	9
731	77	12
732	77	20
733	77	19
734	77	2
735	77	18
736	77	17
737	77	16
738	78	5
739	78	6
740	78	7
741	78	8
742	78	9
743	78	12
744	78	20
745	78	19
746	78	2
747	78	18
748	78	17
749	78	16
750	79	5
751	79	6
752	79	7
753	79	8
754	79	9
755	79	12
756	79	20
757	79	19
758	79	2
759	79	18
760	79	17
761	79	16
762	79	10
763	79	11
764	80	5
765	80	6
766	80	7
767	80	8
768	80	9
769	80	12
770	80	20
771	80	19
772	80	2
773	80	18
774	80	17
775	80	16
776	80	10
777	80	11
778	80	22
779	81	5
780	81	6
781	81	7
782	81	8
783	81	9
784	81	12
785	81	20
786	81	19
787	81	2
788	81	18
789	81	17
790	81	16
791	81	10
792	81	11
793	81	22
794	81	15
795	81	14
796	50	20
797	50	19
798	50	18
799	82	5
800	82	6
801	82	7
802	82	8
803	82	9
804	82	12
805	82	11
806	82	22
807	82	15
808	82	14
809	83	5
810	83	6
811	83	7
812	83	8
813	83	9
814	83	20
815	83	17
816	84	5
817	84	6
818	84	7
819	84	8
820	84	9
821	84	20
822	84	17
823	85	5
824	85	6
825	85	7
826	85	8
827	85	9
\.


--
-- Name: groups_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.groups_id_seq', 21, true);


--
-- Name: groups_skills_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.groups_skills_id_seq', 119, true);


--
-- Name: groups_users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.groups_users_id_seq', 10, true);


--
-- Name: requests_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.requests_id_seq', 25, true);


--
-- Name: skills_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.skills_id_seq', 22, true);


--
-- Name: teachers_conditions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.teachers_conditions_id_seq', 1, false);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 85, true);


--
-- Name: users_skills_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_skills_id_seq', 827, true);


--
-- Name: groups groups_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.groups
    ADD CONSTRAINT groups_pk PRIMARY KEY (id);


--
-- Name: groups_skills groups_skills_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.groups_skills
    ADD CONSTRAINT groups_skills_pk PRIMARY KEY (id);


--
-- Name: groups_users groups_users_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.groups_users
    ADD CONSTRAINT groups_users_pk PRIMARY KEY (id);


--
-- Name: requests requests_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.requests
    ADD CONSTRAINT requests_pk PRIMARY KEY (id);


--
-- Name: skills skills_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.skills
    ADD CONSTRAINT skills_pk PRIMARY KEY (id);


--
-- Name: teachers_conditions teachers_conditions_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.teachers_conditions
    ADD CONSTRAINT teachers_conditions_pk PRIMARY KEY (id);


--
-- Name: users users_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pk PRIMARY KEY (id);


--
-- Name: users_skills users_skills_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users_skills
    ADD CONSTRAINT users_skills_pk PRIMARY KEY (id);


--
-- Name: users_skills_user_id_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX users_skills_user_id_idx ON public.users_skills USING btree (user_id, skill_id);


--
-- Name: groups groups_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.groups
    ADD CONSTRAINT groups_fk FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: groups_skills groups_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.groups_skills
    ADD CONSTRAINT groups_fk FOREIGN KEY (group_id) REFERENCES public.groups(id) ON DELETE CASCADE;


--
-- Name: groups_users groups_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.groups_users
    ADD CONSTRAINT groups_fk FOREIGN KEY (group_id) REFERENCES public.groups(id) ON DELETE CASCADE;


--
-- Name: users_skills skill_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users_skills
    ADD CONSTRAINT skill_fk FOREIGN KEY (skill_id) REFERENCES public.skills(id) ON DELETE CASCADE;


--
-- Name: groups_skills skills_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.groups_skills
    ADD CONSTRAINT skills_fk FOREIGN KEY (skill_id) REFERENCES public.skills(id) ON DELETE CASCADE;


--
-- Name: users_skills user_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users_skills
    ADD CONSTRAINT user_fk FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: groups_users users_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.groups_users
    ADD CONSTRAINT users_fk FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: teachers_conditions users_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.teachers_conditions
    ADD CONSTRAINT users_fk FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

