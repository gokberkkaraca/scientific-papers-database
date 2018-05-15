import java.sql.*;

public class Connector {
    private static Connection conn = null;

    public static void main( String args[]){

        //Load mysql jdbc driver
        loadDriver();

        //Establish connection
        connectDatabase();

        //Drop existing tables
        dropTables();
        System.out.println("\nOld tables dropped");

        //Create tables
        createTables();
        System.out.println("Tables created.");

        //Create Triggers
        createTriggers();
        System.out.println("Triggers created.");

        //Create Views
        createViews();
        System.out.println("Views created");

        //Create Procedures
        createProcedures();
        System.out.println("Procedures created.");

        //Insertions
        insertRows();
        System.out.println("Dummy data created.");

        //Close connection
        closeConnection();


    }

    private static void loadDriver() {
        System.out.println("Loading driver...");
        try {
            // The newInstance() call is a work around for some
            // broken Java implementations
            Class.forName("com.mysql.jdbc.Driver").newInstance();
            System.out.println("Driver loaded...");
        } catch (Exception ex) {
            // handle the error
            System.out.println("Driver load failed!");
            ex.printStackTrace();
        }
    }

    private static void connectDatabase() {

        //Init connection parameters
        String port = "3306";
        String hostName = "dijkstra.ug.bilkent.edu.tr";
        String username = "kenan.asadov";
        String dbName = "kenan_asadov";
        String password = "jdt63r3zi";
        String url = "jdbc:mysql://" + hostName + ":" + port + "/" + dbName;

        System.out.println("Trying to connect database...");
        try {
            conn = DriverManager.getConnection(url, username, password);
            System.out.println("Connected to database.");
        } catch (SQLException e) {
            System.out.println("Connection failed");
            e.printStackTrace();
        }
    }

    private static void execQuery(String tableQuery) {
        //Define query parameters
        Statement stmt = null;
        try {
            stmt = conn.createStatement();
            stmt.execute(tableQuery);
        }
        catch (SQLException ex){
            // handle any errors
            System.out.println("SQLException: " + ex.getMessage());
            System.out.println("SQLState: " + ex.getSQLState());
            System.out.println("VendorError: " + ex.getErrorCode());
            System.out.print(tableQuery);
        }
        finally {
            //Close the statament and reset the parameters
            if (stmt != null) {
                try {
                    stmt.close();
                } catch (SQLException sqlEx) { } // ignore
                stmt = null;
            }
        }
    }

    private static void dropTables() {
        String tableNames[] = { "co_authors","subscription", "authorExpertise", "reviewerExpertise", "reviews", "editorPublisher","published_in",
                "cites", "invites",  "finances" , "sponsor",
                "publication", "audience", "conference","journal_volume","journal", "expertise", "submits", "author","reviewer",
                "publisher"," submission","editor","subscriber","institution" };

        for (String tName: tableNames ) {
            String dropQuery = "DROP TABLE IF EXISTS " + tName;
            execQuery(dropQuery);
        }
    }

    private static void createTables() {
        String institution = "CREATE TABLE institution(\n" +
                "        i_name varchar(200) PRIMARY KEY,\n" +
                "        street_name varchar(50),\n" +
                "                zip_code varchar(10),\n" +
                "                city_name varchar(50),\n" +
                "                country varchar(50) )";

        String subscriber = "CREATE TABLE subscriber(\n" +
                "        email varchar(200),\n" +
                "                i_name varchar(200),\n" +
                "                password varchar(50) NOT NULL,\n" +
                "        s_name varchar(50) NOT NULL,\n" +
                "        s_surname varchar(50) NOT NULL,\n" +
                "        usertype INTEGER NOT NULL,\n" +
                "        FOREIGN KEY(i_name) REFERENCES institution(i_name) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "        PRIMARY KEY(email, i_name))\n" +
                "        ENGINE = INNODB";

        String publisher = "CREATE TABLE publisher( p_name varchar(200) PRIMARY KEY)\n" +
                "        ENGINE = INNODB";

        String author = "CREATE TABLE author(\n" +
                "        email varchar(200) PRIMARY KEY,\n" +
                "        FOREIGN KEY (email) REFERENCES subscriber(email) ON DELETE CASCADE ON UPDATE CASCADE)\n" +
                "        ENGINE = INNODB";

        String editor = "CREATE TABLE editor(\n" +
                "        experience INTEGER,\n" +
                "        email varchar(200) PRIMARY KEY,\n" +
                "        FOREIGN KEY (email) REFERENCES subscriber(email) ON DELETE CASCADE ON UPDATE CASCADE)\n" +
                "        ENGINE = INNODB";

        String reviewer = "CREATE TABLE reviewer(\n" +
                "        email varchar(200) PRIMARY KEY,\n" +
                "        FOREIGN KEY (email) REFERENCES subscriber(email) ON DELETE CASCADE ON UPDATE CASCADE)\n" +
                "        ENGINE = INNODB";

        String submission = "CREATE TABLE submission(\n" +
                "        s_id INT,\n" +
                "        status TINYINT,\n" +
                "        title varchar(500),\n" +
                "                doc_link varchar(200),\n" +
                "                date date,\n" +
                "                email varchar(200),\n" +
                "                FOREIGN KEY (email) REFERENCES editor(email) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                PRIMARY KEY( s_id))\n" +
                "        ENGINE = INNODB";

        String publication = "CREATE TABLE publication(\n" +
                "        p_id INT,\n" +
                "        title varchar(500),\n" +
                "                pages INT,\n" +
                "                publication_date date,\n" +
                "                doc_link varchar(200),\n" +
                "                downloads INT DEFAULT 0,\n" +
                "                s_id INT NOT NULL,\n" +
                "                FOREIGN KEY (s_id) REFERENCES submission(s_id) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                PRIMARY KEY(p_id, s_id))\n" +
                "        ENGINE = INNODB";


        String conference = "CREATE TABLE conference(\n" +
                "        date date,\n" +
                "        conference_topic varchar(200),\n" +
                "                p_name varchar(200) PRIMARY KEY,\n" +
                "        FOREIGN KEY(p_name) REFERENCES publisher(p_name) ON DELETE CASCADE ON UPDATE CASCADE)\n" +
                "        ENGINE = INNODB";

        String journal = "CREATE TABLE journal(\n" +
                "        journal_topic varchar(200),\n" +
                "                p_name varchar(200) PRIMARY KEY,\n" +
                "        FOREIGN KEY (p_name) REFERENCES publisher(p_name) ON DELETE CASCADE ON UPDATE CASCADE)\n" +
                "        ENGINE = INNODB";

        String journalVolume = "CREATE TABLE journal_volume (" +
                "p_name varchar(200), " +
                "volume_no INT," +
                "FOREIGN KEY (p_name) REFERENCES journal(p_name) ON DELETE CASCADE ON UPDATE CASCADE, " +
                "PRIMARY KEY(volume_no, p_name))\n" +
                "ENGINE=INNODB";

        String audience = "CREATE TABLE audience (" +
                "p_name VARCHAR(200)," +
                "a_name VARCHAR(200)," +
                "a_surname VARCHAR(200), " +
                "FOREIGN KEY (p_name) REFERENCES conference(p_name) ON DELETE CASCADE ON UPDATE CASCADE, " +
                "PRIMARY KEY(p_name, a_name, a_surname))" +
                "ENGINE=INNODB";

        String invites = "CREATE TABLE invites(\n" +
                "        reviewer_email varchar(200),\n" +
                "                editor_email varchar(200),\n" +
                "                s_id INT,\n" +
                "                FOREIGN KEY (editor_email) REFERENCES editor(email) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                FOREIGN KEY (reviewer_email) REFERENCES reviewer(email) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                FOREIGN KEY (s_id) REFERENCES submission(s_id) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                status TINYINT DEFAULT 0,\n" +
                "                PRIMARY KEY( reviewer_email, editor_email, s_id))\n" +
                "        ENGINE = INNODB";

        String authorExpertise = "CREATE TABLE authorExpertise( email varchar(200), tag varchar(100), " +
                "FOREIGN KEY (email) REFERENCES author(email) ON DELETE CASCADE ON UPDATE CASCADE," +
                " FOREIGN KEY (tag) REFERENCES expertise(tag) ON DELETE CASCADE ON UPDATE CASCADE," +
                "PRIMARY KEY( email, tag)) ENGINE=INNODB";

        String reviewerExpertise = "CREATE TABLE reviewerExpertise (\n" +
                "        email varchar(200),\n" +
                "                tag varchar(100),\n" +
                "                FOREIGN KEY (email) REFERENCES reviewer(email) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                FOREIGN KEY (tag) REFERENCES expertise(tag) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                PRIMARY KEY(email,tag))\n" +
                "        ENGINE=INNODB";

        String reviews = "CREATE TABLE reviews(\n" +
                "        reviewer_email varchar(200),\n" +
                "                editor_email varchar(200),\n" +
                "                s_id INT NOT NULL,\n" +
                "                feedback varchar(2500),\n" +
                "                FOREIGN KEY (reviewer_email) REFERENCES reviewer(email) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                FOREIGN KEY (editor_email) REFERENCES editor(email) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                FOREIGN KEY (s_id) REFERENCES submission(s_id) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                PRIMARY KEY(reviewer_email, editor_email, s_id))\n" +
                "        ENGINE=INNODB";

        String editorPublisher = "CREATE TABLE editorPublisher (\n" +
                "        email varchar(200),\n" +
                "                p_name varchar(200),\n" +
                "                FOREIGN KEY (email) REFERENCES editor(email) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                FOREIGN KEY (p_name) REFERENCES publisher(p_name) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                PRIMARY KEY(email,p_name))\n" +
                "        ENGINE=INNODB";

        String cites = "CREATE TABLE cites(\n" +
                "        citer INT,\n" +
                "        cited INT,\n" +
                "        FOREIGN KEY (citer) REFERENCES publication(p_id) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "        FOREIGN KEY (cited) REFERENCES publication(p_id) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "        PRIMARY KEY(citer, cited))\n" +
                "        ENGINE=INNODB";

        String published_in = "CREATE TABLE published_in ( \n" +
                "                p_name varchar(200),\n" +
                "                volume_no INT,\n" +
                "                p_id INT,\n" +
                "                FOREIGN KEY (p_name) REFERENCES journal_volume(p_name) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                FOREIGN KEY (volume_no) REFERENCES journal_volume(volume_no) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                FOREIGN KEY (p_id) REFERENCES publication(p_id) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                PRIMARY KEY(p_name, volume_no,p_id)) ENGINE=INNODB;";

        String submits = "CREATE TABLE submits (\n" +
                "        email varchar(200),\n" +
                "                s_id INT,\n" +
                "                p_name varchar(200),\n" +
                "                FOREIGN KEY (email) REFERENCES author(email) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                FOREIGN KEY (s_id) REFERENCES submission(s_id) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                FOREIGN KEY (p_name) REFERENCES publisher(p_name) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                PRIMARY KEY(email, s_id, p_name))\n" +
                "        ENGINE=INNODB";

        String sponsor = "CREATE TABLE sponsor(\n" +
                "        name varchar(200) PRIMARY KEY,\n" +
                "        link varchar(200)  )\n" +
                "        ENGINE = INNODB";

        String finances = "CREATE TABLE finances(\n" +
                "        name varchar(200),\n" +
                "                p_id INT,\n" +
                "                FOREIGN KEY (name) REFERENCES sponsor(name) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                FOREIGN KEY (p_id) REFERENCES publication(p_id) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                PRIMARY KEY(name, p_id))\n" +
                "        ENGINE=INNODB";

        String expertise = "\n" +
                "CREATE TABLE expertise( tag varchar(100) PRIMARY KEY)\n" +
                "ENGINE = INNODB\n";

        String coAuthors = "CREATE TABLE co_authors(\n" +
                "        s_id INT,\n" +
                "        email varchar(200),\n" +
                "        FOREIGN KEY (s_id) REFERENCES submission(s_id) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "        FOREIGN KEY (email) REFERENCES author(email) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                PRIMARY KEY(s_id, email))\n" +
                "        ENGINE=INNODB";

        String subscription = "CREATE TABLE subscription(\n" +
                "        email varchar(200),\n" +
                "        p_name varchar(200)," +
                "        start_date DATE," +
                "        end_date DATE," +
                "        FOREIGN KEY (p_name) REFERENCES journal(p_name) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "        FOREIGN KEY (email) REFERENCES subscriber(email) ON DELETE CASCADE ON UPDATE CASCADE,\n" +
                "                PRIMARY KEY(p_name, email))\n" +
                "        ENGINE=INNODB";

        execQuery(institution);
        execQuery(subscriber);
        execQuery(publisher);
        execQuery(author);
        execQuery(editor);
        execQuery(reviewer);
        execQuery(submission);
        execQuery(publication);
        execQuery(conference);
        execQuery(journal);
        execQuery(invites);
        execQuery(expertise);
        execQuery(authorExpertise);
        execQuery(reviewerExpertise);
        execQuery(reviews);
        execQuery(editorPublisher);
        execQuery(cites);
        execQuery(submits);
        execQuery(sponsor);
        execQuery(finances);
        execQuery(journalVolume);
        execQuery(audience);
        execQuery(published_in);
        execQuery(coAuthors);
        execQuery(subscription);
    }


    private static void insertRows() {
        insertInstitutions();
        insertSubscribers();
        insertPublishers();
        insertAuthors();
        insertEditors();
        insertReviewers();
        insertSubmissions();
        insertPublications();
        insertConference();
        insertSubmits();
        insertJournals();
        insertInvites();
        insertExpertises();
        insertAuthorExpertises();
        insertReviewerExpertises();
        insertReviews();
        insertEditorPublishers();
        insertCites();
        insertSponsors();
        insertFinances();
        insertJournalVolumes();
        insertAudiences();
        insertPublishedIns();
    }

    private static void insertExpertises() {
        System.out.println("Insert expertise");
        String insertQuery = "INSERT INTO expertise VALUES";
        String query1 = "('Computer Science')";
        String query2 = "('Chemistry')";
        String query3 = "('Economy')";
        String query4 = "('Bioglogy')";
        String query5 = "('Physics')";
        String query6 = "('Law')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);

        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
    }


    private static void insertConference() {
        System.out.println("In conference");
        String insertQuery = "INSERT INTO conference VALUES";
        String query1 = "('2017.01.01', 'NIPS : Neural Information Processing Systems', 'TED Conferences')";
        String query2 = "('2018.01.02', '31st International Microprocesses and Nanotechnology Conference', 'Harvard Conferences')";
        String query3 = "('2019.01.03', '45th annual European Physical Society Conference on Plasma Physics', 'Bilkent Conferences')";
        String query4 = "('2019.01.03', 'ECCV : European Conference on Computer Vision', 'EPFL Conferences')";
        String query5 = "('2019.01.03', 'EPoS 2018 The Early Phase of Star Formation - Archetypes', 'Sabanci Conferences')";
        String query6 = "('2019.01.03', 'European Data Provider Forum and Training Event 2018', 'MIT Conferences')";
        String query7 = "('2019.01.03', 'Mathematical General Relativity', 'ITU Conferences')";
        String query8 = "('2019.01.03', 'AAAI : AAAI Conference on Artificial Intelligence', 'Oxford Conferences')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
        execQuery( insertQuery + query7);
        execQuery( insertQuery + query8);
    }


    private static void insertJournals() {
        System.out.println("In journals");
        String insertQuery = "INSERT INTO journal VALUES";
        String query1 = "('Computer Science', 'Bilkent Journals')";
        String query2 = "('Computer Science/Physics', 'Stanford Journals')";
        String query3 = "('Chemistry', 'METU Journals')";
        String query4 = "('Law/Economy', 'Harvard Journals')";
        String query5 = "('Computer Science', 'MIT Journals')";


        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
    }

    private static void insertPublishers() {
        System.out.println("In publishers");
        String insertQuery = "INSERT INTO publisher VALUES";
        String query1 ="('TED Conferences')";
        String query2 ="('Harvard Conferences')";
        String query3 ="('Bilkent Conferences')";
        String query4 ="('EPFL Conferences')";


        String query5 ="('Sabanci Conferences')";
        String query6 ="('MIT Conferences')";
        String query7 ="('ITU Conferences')";
        String query8 ="('Oxford Conferences')";

        String query9 ="('Bilkent Journals')";
        String query10 ="('Stanford Journals')";
        String query11 ="('METU Journals')";
        String query12 ="('Harvard Journals')";
        String query13 ="('MIT Journals')";


        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
        execQuery( insertQuery + query7);
        execQuery( insertQuery + query8);
        execQuery( insertQuery + query9);
        execQuery( insertQuery + query10);
        execQuery( insertQuery + query11);
        execQuery( insertQuery + query12);
        execQuery( insertQuery + query13);
    }

    private static void insertReviewers() {
        System.out.println("In reviews");
        String insertQuery = "INSERT INTO reviewer VALUES";
        String query1 ="('tonybarnosky@standford.edu')";
        String query2 ="('poole@cs.epfl.edu')";
        String query3 ="('guvenir@cs.bilent.edu.tr')";
        String query4 ="('evans@cs.epfl.edu')";
        String query5 ="('light@physics.harvard.edu')";
        String query6 ="('isler@econ.bilkent.edu')";
        String query7 ="('akkaya@bilkent.edu.tr')";
        String query8 ="('oulusoy@cs.bilkent.edu.tr')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
        execQuery( insertQuery + query7);
        execQuery( insertQuery + query8);
    }

    private static void insertAuthors() {
        String insertQuery = "INSERT INTO author VALUES";
        String query1 ="('acirpan@metu.edu.tr')";
        String query2 ="('ugur@cs.bilkent.edu.tr')";
        String query3 ="('cicek@cs.bilkent.edu.tr')";
        String query4 ="('alford@hls.harvard.edu')";
        String query5 ="('demler@physics.harvard.edu')";
        String query6 ="('aiken@cs.strandford.edu')";
        String query7 ="('bblock@standford.edu')";
        String query8 ="('melisa.terazi@tto.re')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
        execQuery( insertQuery + query7);
        execQuery( insertQuery + query8);
    }

    private static void insertEditors() {
        String insertQuery = "INSERT INTO editor VALUES";
        String query1 ="('1', 'kocabiyik@boun.edu.tr')";
        String query2 ="('2', 'abramson@harvard.edu')";
        String query3 ="('3', 'korpe@cs.bilkent.edu.tr')";
        String query4 ="('4', 'abraham.donut@gmail.com')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
    }

    private static void insertSubscribers() {
        System.out.println("In subscribers");
        String insertQuery = "INSERT INTO subscriber VALUES";

        //Authors
        String query1 = "('acirpan@metu.edu.tr', 'Middle East Techical University', 'alicirpan', 'Ali', 'Cirpan', '2')";
        String query2 = "('ugur@cs.bilkent.edu.tr', 'Bilkent University', 'ugurdogrusoz', 'Ugur', 'Dogrusoz', '2')";
        String query3 = "('cicek@cs.bilkent.edu.tr', 'Bilkent University', 'ercumentcicek', 'Abdullah Ercument', 'Cicek', '2')";
        String query4 = "('alford@hls.harvard.edu', 'Harvard University', 'williamalford', 'William', 'Alford', '2')";
        String query5 = "('demler@physics.harvard.edu', 'Harvard University', 'eugenedemler', 'Eugune', 'Demler', '2')";
        String query6 = "('aiken@cs.strandford.edu', 'Stanford University', 'alexaiken', 'Alex', 'Aikent', '2');";
        String query7 = "('bblock@standford.edu', 'Stanford University', 'barbarablock', 'Barbara', 'Block', '2');";
        String query8 = "('melisa.terazi@tto.re', 'Sabanci University', 'melisaterazi', 'Melisa', 'Terazi', '2')";

        //Editors
        String query9 = "('kocabiyik@boun.edu.tr', 'Bogazici University', 'ergunkocabiyik', 'Ergun', 'kocabiyik', '3')";
        String query10 = "('abramson@harvard.edu', 'Harvard University', 'jillabramson', 'Jill', 'Abromson', '3')";
        String query11 = "('korpe@cs.bilkent.edu.tr', 'Bilkent University', 'ibrahimkorpeoglu', 'Ibrahim', 'Korpeoglu', '3')";
        String query12 = "('abraham.donut@gmail.com', 'Stanford University', 'abrahamdonut', 'Abraham', 'DOnut', '3')";


        //Reviewers
        String query13 = "('tonybarnosky@standford.edu', 'Stanford University', 'tonybarnosky', 'Tony', 'Barnosky', '1')";
        String query14 = "('poole@cs.epfl.edu', 'Ecole Polytechnique Federale de Lausanne', 'chrispoole', 'Chris', 'Poole', '1')";
        String query15 = "('guvenir@cs.bilent.edu.tr', 'Bilkent University', 'halilaltayguvenir', 'Halil Altay', 'Guvenir', '1')";
        String query16 = "('evans@cs.epfl.edu', 'Ecole Polytechnique Federale de Lausanne', 'tonyevans', 'Tony', 'Evans', '1')";
        String query17 = "('light@physics.harvard.edu', 'Harvard University', 'robertlight', 'Robert', 'Light', '1')";
        String query18 = "('isler@econ.bilkent.edu', 'Bilkent University', 'gulisler', 'Gul', 'Isler', '1')";
        String query19 = "('akkaya@bilkent.edu.tr', 'Bilkent University', 'sahinakkaya', 'Sahin', 'Akkaya', '1')";
        String query20 = "('oulusoy@cs.bilkent.edu.tr', 'Bilkent University', 'ozgurulusoy', 'Ozgur', 'Ulusoy', '1')";

        //Regular Users
        String query21 = "('kaan.sancak@ug.bilkent.edu.tr', 'Bilkent University', 'kaan', 'Kaan', 'Sancak', '0')";
        String query22 = "('ali.sabbagh@ug.bilkent.edu.tr', 'Bilkent University', 'ali', 'Ali', 'Sabbagh', '0')";
        String query23 = "('gokberk.karaca@ug.bilkent.edu.tr', 'Bilkent University', 'gokberk', 'Gokberk', 'Karaca', '0')";
        String query24 = "('kenan.asadov@ug.bilkent.edu.tr', 'Bilkent University', 'kenan', 'Kenan', 'Asadov', '0')";


        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
        execQuery( insertQuery + query7);
        execQuery( insertQuery + query8);
        execQuery( insertQuery + query9);
        execQuery( insertQuery + query10);
        execQuery( insertQuery + query11);
        execQuery( insertQuery + query12);
        execQuery( insertQuery + query13);
        execQuery( insertQuery + query14);
        execQuery( insertQuery + query15);
        execQuery( insertQuery + query16);
        execQuery( insertQuery + query17);
        execQuery( insertQuery + query18);
        execQuery( insertQuery + query19);
        execQuery( insertQuery + query20);
        execQuery( insertQuery + query21);
        execQuery( insertQuery + query22);
        execQuery( insertQuery + query23);
        execQuery( insertQuery + query24);
    }


    private static void insertSponsors() {
        System.out.println("In sponsors");
        String insertQuery = "INSERT INTO sponsor VALUES";
        String query1 = "('IEEE', 'https://www.ieee.org/')";
        String query2 = "('Google', 'https://scholar.google.com')";
        String query3 = "('Sage', 'https://us.sagepub.com/en-us/nam/change-location/0')";
        String query4 = "('Elsevier', 'https://www.elsevier.com/events/conferences/world-congress-on-biosensors/exhibitors-and-sponsors/sponsors')";
        String query5 = "('Bio Sensors', 'https://www.biosensors.com/intl/')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
    }

    private static void insertFinances() {
        String insertQuery = "INSERT INTO finances VALUES";
        String query1 = "('IEEE', '11')";
        String query2 = "('Google', '12')";
        String query3 = "('Sage', '13')";
        String query4 = "('Elsevier', '14')";
        String query5 = "('Bio Sensors', '15')";
        String query6 = "('IEEE', '16')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
    }

    private static void insertInvites() {
        String insertQuery = "INSERT INTO invites VALUES";
        String query1 = "('tonybarnosky@standford.edu', 'kocabiyik@boun.edu.tr', '4', '0')";
        String query2 = "('tonybarnosky@standford.edu', 'abramson@harvard.edu', '5', '1')";
        String query3 = "('poole@cs.epfl.edu', 'kocabiyik@boun.edu.tr', '6', '2')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
    }

    private static void insertInstitutions() {
        String insertQuery = "INSERT INTO institution VALUES";
        String query1 = "('Middle East Techical University', 'Universiteler Mahallesi', '06800', 'Ankara', 'Turkey')";
        String query2 = "('Bilkent University', 'Universiteler Mahallesi', '06800', 'Ankara', 'Turkey')";
        String query3 = "('Bogazici University', 'Besiktas', '340000', 'Istanbul', 'Turkey')";
        String query4 = "('Harvard University', 'Cambridge', '02138', 'Massachussets', 'USA')";
        String query5 = "('Stanford University', 'Serra Mall', '94305', 'California', 'USA')";
        String query6 = "('Ecole Polytechnique Federale de Lausanne', 'Route Cantonale', '1015', 'Lausanne', 'Switzerland')";
        String query7 = "('Sabanci University', 'Orhanli Mahallesi', '343456', 'Istanbul', 'Turkey')";


        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
        execQuery( insertQuery + query7);
    }

    private static void insertPublications() {
        String insertQuery = "INSERT INTO publication VALUES";
        String query1 ="('11', 'Influence Estimation and Maximization', '122', '2018.01.05', 'link1', '1', '1')";
        String query2 ="('12', 'Highly-Scalable Deep Convolutional Neural Network', '431', '2018.03.30', 'link2', '2', '2')";
        String query3 ="('13', 'Development of Interest Estimation Tool for Effective HAI', '321', '1970.01.03', 'link3', '3', '3')";
        String query4 ="('14', 'SynFlo: A Tangible Museum Exhibit', '432', '1970.01.04', 'link4', '4', '4')";
        String query5 ="('15', 'Database research at Bilkent University', '542', '1970.01.05', 'link5', '5', '5')";
        String query6 ="('16', 'LTE radio analytics made easy and accessible', '145', '1970.01.06', 'link6', '6', '6')";
        String query7 ="('17', 'Accurate indoor localization with zero start-up cost', '122', '2018.01.05', 'link1', '1', '1')";
        String query8 ="('18', 'Full duplex radios: from impossibility to practice', '431', '2018.03.30', 'link2', '2', '2')";
        String query9 ="('19', 'Dynamic assembly of views in data cubes', '321', '1970.01.03', 'link3', '3', '3')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
        execQuery( insertQuery + query7);
        execQuery( insertQuery + query8);
        execQuery( insertQuery + query9);
    }



    private static void insertAuthorExpertises() {
        String insertQuery = "INSERT INTO authorExpertise VALUES";
        String query1 ="('acirpan@metu.edu.tr', 'Computer Science')";
        String query2 ="('ugur@cs.bilkent.edu.tr', 'Chemistry')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
    }

    private static void insertReviewerExpertises() {
        String insertQuery = "INSERT INTO reviewerExpertise VALUES";
        String query1 ="('tonybarnosky@standford.edu', 'Computer Science')";
        String query2 ="('poole@cs.epfl.edu', 'Chemistry')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
    }


    private static void insertReviews() {
        String insertQuery = "INSERT INTO reviews VALUES";
        String query1 ="('tonybarnosky@standford.edu', 'kocabiyik@boun.edu.tr', '1', 'Good job.')";
        String query2 ="('poole@cs.epfl.edu', 'abramson@harvard.edu', '2', 'Please change the title.')";
        String query3 ="('tonybarnosky@standford.edu', 'kocabiyik@boun.edu.tr', '3', 'Can you add more references?')";
        String query4 ="('poole@cs.epfl.edu', 'kocabiyik@boun.edu.tr', '4', 'This topic does not seem relevant.')";
        String query5 ="('tonybarnosky@standford.edu', 'kocabiyik@boun.edu.tr', '5', 'Font size is not suitable for that publisher.')";
        String query6 ="('poole@cs.epfl.edu', 'abramson@harvard.edu', '6', 'Submission is approved.')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
    }


    private static void insertEditorPublishers() {
        String insertQuery = "INSERT INTO editorPublisher VALUES";
        String query1 ="('kocabiyik@boun.edu.tr', 'TED Conferences')";
        String query2 ="('abramson@harvard.edu', 'Harvard Conferences')";
        String query3 ="('kocabiyik@boun.edu.tr', 'Bilkent Conferences')";
        String query4 ="('abramson@harvard.edu', 'Bilkent Journals')";
        String query5 ="('kocabiyik@boun.edu.tr', 'Stanford Journals')";
        String query6 ="('abramson@harvard.edu', 'METU Journals')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
    }


    private static void insertCites() {
        String insertQuery = "INSERT INTO cites VALUES";
        String query1 ="('11', '12')";
        String query2 ="('11', '13')";
        String query3 ="('13', '12')";
        String query4 ="('11', '18')";
        String query5 ="('14', '12')";
        String query6 ="('14', '11')";
        String query7 ="('15', '16')";
        String query8 ="('11', '15')";
        String query9 ="('14', '16')";
        String query10 ="('14', '18')";
        String query11 ="('15', '12')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
        execQuery( insertQuery + query7);
        execQuery( insertQuery + query8);
        execQuery( insertQuery + query9);
        execQuery( insertQuery + query10);
        execQuery( insertQuery + query11);
    }


    private static void insertSubmissions() {
        String insertQuery = "INSERT INTO submission VALUES";
        String query1 ="('1', '4', 'Influence Estimation and Maximization', 'link1', '1970.01.01', 'kocabiyik@boun.edu.tr')";
        String query2 ="('2', '4', 'Highly-Scalable Deep Convolutional Neural Network',  'link2', '1970.01.02','abramson@harvard.edu')";
        String query3 ="('3', '4', 'Development of Interest Estimation Tool for Effective HAI', 'link3','1970.01.03',  'kocabiyik@boun.edu.tr')";
        String query4 ="('4', '4', 'SynFlo: A Tangible Museum Exhibit', 'link4','1970.01.04',  'abramson@harvard.edu')";
        String query5 ="('5', '4', 'Database research at Bilkent University', 'link5', '1970.01.05',  'kocabiyik@boun.edu.tr')";
        String query6 ="('6', '4', 'LTE radio analytics made easy and accessible', 'link6','1970.01.06',  'abramson@harvard.edu')";
        String query7 ="('7', '4', 'Accurate indoor localization with zero start-up cost', 'link7','1970.01.07',  'kocabiyik@boun.edu.tr')";
        String query8 ="('8', '4', 'Full duplex radios: from impossibility to practice', 'link8','1970.01.08',  'abramson@harvard.edu')";
        String query9 ="('9', '4', 'Dynamic assembly of views in data cubes', 'link9', '1970.01.09', 'kocabiyik@boun.edu.tr')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
        execQuery( insertQuery + query7);
        execQuery( insertQuery + query8);
        execQuery( insertQuery + query9);
    }

    private static void insertSubmits() {
        String insertQuery = "INSERT INTO submits VALUES";
        String query1 ="('acirpan@metu.edu.tr', '1', 'TED Conferences')";
        String query2 ="('ugur@cs.bilkent.edu.tr', '2', 'Harvard Conferences')";
        String query3 ="('acirpan@metu.edu.tr', '3', 'Bilkent Conferences')";
        String query4 ="('ugur@cs.bilkent.edu.tr', '4', 'Bilkent Journals')";
        String query5 ="('acirpan@metu.edu.tr', '5', 'Stanford Journals')";
        String query6 ="('ugur@cs.bilkent.edu.tr', '6', 'METU Journals')";
        String query7 ="('acirpan@metu.edu.tr', '7', 'TED Conferences')";
        String query8 ="('ugur@cs.bilkent.edu.tr', '8', 'Harvard Conferences')";
        String query9 ="('acirpan@metu.edu.tr', '9', 'Bilkent Conferences')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
        execQuery( insertQuery + query7);
        execQuery( insertQuery + query8);
        execQuery( insertQuery + query9);
    }

    private static void insertJournalVolumes() {
        String insertQuery = "INSERT INTO journal_volume VALUES";
        String query1 ="('Bilkent Journals', '1')";
        String query2 ="('Bilkent Journals', '2')";
        String query3 ="('Bilkent Journals', '3')";
        String query4 ="('Stanford Journals', '1')";
        String query5 ="('Stanford Journals', '2')";
        String query6 ="('Stanford Journals', '3')";
        String query7 ="('METU Journals', '1')";
        String query8 ="('METU Journals', '2')";
        String query9 ="('METU Journals', '3')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
        execQuery( insertQuery + query7);
        execQuery( insertQuery + query8);
        execQuery( insertQuery + query9);
    }

    private static void insertPublishedIns() {
        String insertQuery = "INSERT INTO published_in VALUES";
        String query1 ="('Bilkent Journals', '3', 14)";
        String query2 ="('Stanford Journals', '3', 15)";
        String query3 ="('METU Journals', '3', 16)";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
    }


    private static void insertAudiences() {
        String insertQuery = "INSERT INTO audience VALUES";
        String query1 ="('TED Conferences', 'Ahmet', 'Candiroglu')";
        String query2 ="('TED Conferences', 'Clara', 'Book')";
        String query3 ="('TED Conferences', 'William', 'Sawyer')";
        String query4 ="('Harvard Conferences', 'Tomas', 'Alone')";
        String query5 ="('Harvard Conferences', 'Ali', 'Demler')";
        String query6 ="('Harvard Conferences', 'Deniz', 'Alniacik')";
        String query7 ="('Bilkent Conferences', 'Meltem', 'Rüzgar')";
        String query8 ="('Bilkent Conferences', 'Ulas', 'Yoluacik')";
        String query9 ="('Bilkent Conferences', 'Danny', 'Unforgettable')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
        execQuery( insertQuery + query7);
        execQuery( insertQuery + query8);
        execQuery( insertQuery + query9);
    }

    private static void createTriggers(){
        String updateSubmissionStatus = "CREATE TRIGGER update_submission  \n" +
                "AFTER DELETE ON  invites\n" +
                "FOR EACH ROW  \n" +
                "BEGIN\n" +
                "update submission set status = 2 where s_id = old.s_id AND s_id not in( select s_id from invites)" +
                "AND s_id in( select s_id from reviews );\n" +
                "END;";
        execQuery(updateSubmissionStatus);
        System.out.println(updateSubmissionStatus);
    }

    private static void createProcedures(){
        String dropInsertSubmission = "drop procedure if exists insert_submission";
        String dropInsertPublication = "drop procedure if exists insert_publication";
        String dropPublicationCount = "drop procedure if exists find_number_of_publications";
        String dropFindNumberOfCitations = "drop procedure if exists find_author_total_citations";

        execQuery(dropInsertSubmission);
        execQuery(dropInsertPublication);
        execQuery(dropPublicationCount);
        execQuery(dropFindNumberOfCitations);


        String insertSubmission = "CREATE PROCEDURE insert_submission\n" +
        "                 (IN title varchar(200), IN doc_link varchar(200), IN email_in VARCHAR(200), IN in_publisher_name VARCHAR(200))\n" +
        "                BEGIN\n" +
        "                DECLARE s_id_val INT DEFAULT 1;\n" +
        "                DECLARE email_editor VARCHAR(200);\n" +
        "\n" +
        "                SELECT (max(s_id) + 1) INTO s_id_val\n" +
        "                FROM submission;\n" +
        "\n" +
        "                select  email into email_editor FROM editor natural join publisher WHERE p_name = in_publisher_name ORDER BY RAND() limit 1;\n" +
        "\n" +
        "                INSERT INTO submission(s_id, `status`, title, doc_link, `date`, email)\n" +
        "                VALUES(`s_id_val`, 0, `title`, `doc_link`, CURDATE(), `email_editor`);\n" +
        "\n" +
        "                INSERT INTO submits (email,s_id, p_name)\n" +
        "                \tVALUES(`email_in`,`s_id_val`, `in_publisher_name`);\n" +
        "                END";


        String insertPublication = "CREATE PROCEDURE insert_publication\n" +
                "     (IN title varchar(200), IN pages INT, IN doc_link varchar(200), IN in_s_id varchar(200))\n" +
                "BEGIN\n" +
                "    DECLARE p_id_val INT DEFAULT 1;\n" +
                "    DECLARE publisher_name VARCHAR(200);\n" +
                "    DECLARE volume_number INT;\n" +
                "    DECLARE is_journal INT DEFAULT 0;\n" +
                "\n" +
                "    SELECT (max(p_id) + 1) INTO p_id_val\n" +
                "    FROM publication;\n" +
                "    \n" +
                "    SELECT p_name into publisher_name FROM submits NATURAL JOIN journal WHERE s_id = in_s_id;\n" +
                "    SELECT max(volume_no) into volume_number FROM journal_volume WHERE p_name = publisher_name;\n" +
                "    SELECT count(*) into is_journal  FROM journal WHERE p_name = publisher_name;\n" +
                "        \n" +
                "\n" +
                "    UPDATE submission set status = 4 WHERE s_id = in_s_id;\n" +
                "    \n" +
                "    \n" +
                "    INSERT INTO publication\n" +
                "    VALUES(p_id_val, title, pages, CURDATE(), doc_link, 0, in_s_id);\n" +
                "    \n" +
                "\tif is_journal > 0\n" +
                "\t\tthen\n" +
                "        INSERT INTO published_in values(publisher_name, volume_number, p_id_val);\n" +
                "\tend if;\n" +
                "    \n" +
                "    \n" +
                "END";


        String publicationCount =
                "\n" +
                        "CREATE PROCEDURE find_number_of_publications\n" +
                        "     (IN author_email VARCHAR(200), OUT total_count INT)\n" +
                        "BEGIN\n" +
                        "    DECLARE author_count INT DEFAULT 0;\n" +
                        "    DECLARE co_author_count INT DEFAULT 0;\n" +
                        "\n" +
                        "    SELECT count(*) as count INTO author_count\n" +
                        "    FROM publication NATURAL JOIN submits\n" +
                        "    WHERE email = author_email;\n" +
                        "    \n" +
                        "    SELECT count(*) as count INTO co_author_count\n" +
                        "    FROM co_authors\n" +
                        "    WHERE email = author_email;\n" +
                        "\n" +
                        "    SET total_count = author_count + co_author_count;\n" +
                        "END\n";



        String citationCount = "CREATE PROCEDURE find_author_total_citations\n" +
                "     (IN author_email VARCHAR(200), OUT author_total_cited INT)\n" +
                "BEGIN\n" +
                "    DECLARE total_count INT DEFAULT 0;\n" +
                "    \n" +
                "    SELECT count(cited) into total_count\n" +
                "    FROM authors_cited\n" +
                "    GROUP BY email\n" +
                "    HAVING email = author_email;\n" +
                "    SET author_total_cited = total_count;\n" +
                "    \n" +
                "    END";

        String mostPopular = "CREATE PROCEDURE find_most_popular\n" +
                "     (IN author_email VARCHAR(200), OUT most_popular_p_id INT)\n" +
                "BEGIN\n" +
                "    DECLARE popular INT DEFAULT 0;\n" +
                "    \n" +
                "    SELECT cited into popular FROM(\n" +
                "\t\tSELECT cited, count(*) as total_count\n" +
                "\t\tFROM authors_cited\n" +
                "\t\tGROUP BY cited\n" +
                "\t\tORDER BY total_count DESC limit 1) as popular_cited;\n" +
                "    \n" +
                "    \n" +
                "    SET most_popular = popular;\n" +
                "    \n" +
                "END";

        System.out.println();
        execQuery(insertSubmission);
        execQuery(insertPublication);
        execQuery(publicationCount);
        execQuery(citationCount);
        //execQuery(mostPopular);
    }

    private static void createViews(){
        String drop = "drop view if exists authors_cited";
        execQuery(drop);

        String view = "\tCREATE VIEW authors_cited(email, cited) AS\n" +
                "\t(SELECT co_authors.email, cited \n" +
                "\tFROM (cites, publication, co_authors)\n" +
                "\tWHERE cited = publication.p_id AND co_authors.s_id = publication.s_id\n" +
                "\t) UNION \n" +
                "\t(SELECT submits.email, cited\n" +
                "\tFROM cites, publication, submits\n" +
                "\tWHERE cited = publication.p_id AND submits.s_id = publication.s_id );";
        execQuery(view);
    }

    private static void createSecondaryIndices(){
        String table1 = "CREATE INDEX search_title ON table publication(title)";
        String table2 = "CREATE INDEX search_date ON table publication(date)";
        String table3 = "CREATE INDEX author_name ON table subscriber(a_name)";

        execQuery(table1);
        execQuery(table2);
        execQuery(table3);
    }


    private static void closeConnection() {
        try {
            conn.close();
            conn = null;
            System.out.println("\nConnection closed.");
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }
}
