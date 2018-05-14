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
        String username = "gokberk.karaca";
        String dbName = "gokberk_karaca";
        String password = "pnsank5";
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
                "                PRIMARY KEY( s_id, email))\n" +
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

    private static void insertSubscribers() {
        String insertQuery = "INSERT INTO subscriber VALUES";
        String query1 = "('email1', 'institution1', 'password1', 'name1', 'surname1', '2')";
        String query2 = "('email2', 'institution2', 'password2', 'name2', 'surname2', '2')";
        String query3 = "('email3', 'institution3', 'password3', 'name3', 'surname3', '3')";
        String query4 = "('email4', 'institution4', 'password4', 'name4', 'surname4', '3')";
        String query5 = "('email5', 'institution5', 'password5', 'name5', 'surname5', '1')";
        String query6 = "('email6', 'institution6', 'password6', 'name6', 'surname6', '1')";


        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
    }

    private static void insertConference() {
        String insertQuery = "INSERT INTO conference VALUES";
        String query1 = "('2017.01.01', 'topic1', 'publisher1')";
        String query2 = "('2018.01.02', 'topic2', 'publisher2')";
        String query3 = "('2019.01.03', 'topic3', 'publisher3')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
    }


    private static void insertExpertises() {
        String insertQuery = "INSERT INTO expertise VALUES";
        String query1 = "('expertise1')";
        String query2 = "('expertise2')";
        String query3 = "('expertise3')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
    }

    private static void insertJournals() {
        String insertQuery = "INSERT INTO journal VALUES";
        String query1 = "('topic1', 'publisher4')";
        String query2 = "('topic2', 'publisher5')";
        String query3 = "('topic3', 'publisher6')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
    }
    private static void insertSponsors() {
        String insertQuery = "INSERT INTO sponsor VALUES";
        String query1 = "('sponsor1', 'link1')";
        String query2 = "('sponsor2', 'link2')";
        String query3 = "('sponsor3', 'link3')";
        String query4 = "('sponsor4', 'link4')";
        String query5 = "('sponsor5', 'link5')";
        String query6 = "('sponsor6', 'link6')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
    }

    private static void insertFinances() {
        String insertQuery = "INSERT INTO finances VALUES";
        String query1 = "('sponsor1', '11')";
        String query2 = "('sponsor2', '12')";
        String query3 = "('sponsor3', '13')";
        String query4 = "('sponsor4', '14')";
        String query5 = "('sponsor5', '15')";
        String query6 = "('sponsor6', '16')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
    }

    private static void insertInvites() {
        String insertQuery = "INSERT INTO invites VALUES";
        String query1 = "('email5', 'email3', '4', '0')";
        String query2 = "('email5', 'email4', '5', '1')";
        String query3 = "('email6', 'email3', '6', '2')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
    }

    private static void insertInstitutions() {
        String insertQuery = "INSERT INTO institution VALUES";
        String query1 = "('institution1', 'street1', 'zip1', 'city1', 'country1')";
        String query2 = "('institution2', 'street2', 'zip2', 'city2', 'country2')";
        String query3 = "('institution3', 'street3', 'zip3', 'city3', 'country3')";
        String query4 = "('institution4', 'street4', 'zip4', 'city4', 'country4')";
        String query5 = "('institution5', 'street5', 'zip5', 'city5', 'country5')";
        String query6 = "('institution6', 'street6', 'zip6', 'city6', 'country6')";


        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
    }

    private static void insertPublications() {
        String insertQuery = "INSERT INTO publication VALUES";
        String query1 ="('11', 'publication1', '581', '1970.01.01', 'link1', '1', '1')";
        String query2 ="('12', 'publication2', '582', '1970.01.02', 'link2', '2', '2')";
        String query3 ="('13', 'publication3', '583', '1970.01.03', 'link3', '3', '3')";
        String query4 ="('14', 'publication4', '584', '1970.01.04', 'link4', '4', '4')";
        String query5 ="('15', 'publication5', '585', '1970.01.05', 'link5', '5', '5')";
        String query6 ="('16', 'publication6', '586', '1970.01.06', 'link6', '6', '6')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
    }

    private static void insertAuthors() {
        String insertQuery = "INSERT INTO author VALUES";
        String query1 ="('email1')";
        String query2 ="('email2')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
    }

    private static void insertAuthorExpertises() {
        String insertQuery = "INSERT INTO authorExpertise VALUES";
        String query1 ="('email1', 'expertise1')";
        String query2 ="('email2', 'expertise2')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
    }

    private static void insertReviewerExpertises() {
        String insertQuery = "INSERT INTO reviewerExpertise VALUES";
        String query1 ="('email5', 'expertise1')";
        String query2 ="('email6', 'expertise2')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
    }

    private static void insertReviewers() {
        String insertQuery = "INSERT INTO reviewer VALUES";
        String query1 ="('email5')";
        String query2 ="('email6')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
    }

    private static void insertReviews() {
        String insertQuery = "INSERT INTO reviews VALUES";
        String query1 ="('email5', 'email3', '1', 'feedback1')";
        String query2 ="('email6', 'email4', '2', 'feedback2')";
        String query3 ="('email5', 'email3', '3', 'feedback3')";
        String query4 ="('email6', 'email3', '4', 'feedback4')";
        String query5 ="('email5', 'email3', '5', 'feedback5')";
        String query6 ="('email6', 'email4', '6', 'feedback6')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
    }


    private static void insertEditorPublishers() {
        String insertQuery = "INSERT INTO editorPublisher VALUES";
        String query1 ="('email3', 'publisher1')";
        String query2 ="('email4', 'publisher2')";
        String query3 ="('email3', 'publisher3')";
        String query4 ="('email4', 'publisher4')";
        String query5 ="('email3', 'publisher5')";
        String query6 ="('email4', 'publisher6')";

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
        String query4 ="('14', '15')";
        String query5 ="('14', '16')";
        String query6 ="('14', '11')";
        String query7 ="('15', '16')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
        execQuery( insertQuery + query7);
    }


    private static void insertPublishers() {
        String insertQuery = "INSERT INTO publisher VALUES";
        String query1 ="('publisher1')";
        String query2 ="('publisher2')";
        String query3 ="('publisher3')";
        String query4 ="('publisher4')";
        String query5 ="('publisher5')";
        String query6 ="('publisher6')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
    }

    private static void insertEditors() {
        String insertQuery = "INSERT INTO editor VALUES";
        String query1 ="('1', 'email3')";
        String query2 ="('2', 'email4')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
    }

    private static void insertSubmissions() {
        String insertQuery = "INSERT INTO submission VALUES";
        String query1 ="('1', '0', 'sub1', 'link1', '1970.01.01', 'email3')";
        String query2 ="('2', '1', 'sub2',  'link2', '1970.01.02','email4')";
        String query3 ="('3', '0', 'sub3', 'link3','1970.01.03',  'email3')";
        String query4 ="('4', '1', 'sub4', 'link4','1970.01.04',  'email4')";
        String query5 ="('5', '2', 'sub5', 'link5', '1970.01.05',  'email3')";
        String query6 ="('6', '1', 'sub6', 'link6','1970.01.06',  'email4')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
    }

    private static void insertSubmits() {
        String insertQuery = "INSERT INTO submits VALUES";
        String query1 ="('email1', '1', 'publisher1')";
        String query2 ="('email2', '2', 'publisher2')";
        String query3 ="('email1', '3', 'publisher3')";
        String query4 ="('email2', '4', 'publisher4')";
        String query5 ="('email1', '5', 'publisher5')";
        String query6 ="('email2', '6', 'publisher6')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
    }

    private static void insertJournalVolumes() {
        String insertQuery = "INSERT INTO journal_volume VALUES";
        String query1 ="('publisher4', '1')";
        String query2 ="('publisher4', '2')";
        String query3 ="('publisher4', '3')";
        String query4 ="('publisher5', '1')";
        String query5 ="('publisher5', '2')";
        String query6 ="('publisher5', '3')";
        String query7 ="('publisher6', '1')";
        String query8 ="('publisher6', '2')";
        String query9 ="('publisher6', '3')";

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
        String query1 ="('publisher4', '1', 11)";
        String query2 ="('publisher4', '2', 12)";
        String query3 ="('publisher4', '3', 13)";
        String query4 ="('publisher5', '1', 14)";
        String query5 ="('publisher5', '2', 15)";
        String query6 ="('publisher5', '3', 16)";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
    }


    private static void insertAudiences() {
        String insertQuery = "INSERT INTO audience VALUES";
        String query1 ="('publisher1', 'audience_name1', 'audience_surname1')";
        String query2 ="('publisher1', 'audience_name2', 'audience_surname2')";
        String query3 ="('publisher1', 'audience_name3', 'audience_surname3')";
        String query4 ="('publisher2', 'audience_name4', 'audience_surname4')";
        String query5 ="('publisher2', 'audience_name5', 'audience_surname5')";
        String query6 ="('publisher2', 'audience_name6', 'audience_surname6')";
        String query7 ="('publisher3', 'audience_name7', 'audience_surname7')";
        String query8 ="('publisher3', 'audience_name8', 'audience_surname8')";
        String query9 ="('publisher3', 'audience_name9', 'audience_surname9')";

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
    }

    private static void createProcedures(){
        String dropInsertSubmission = "drop procedure if exists insert_submission";
        String dropInsertPublication = "drop procedure if exists insert_publication";

        execQuery(dropInsertSubmission);
        execQuery(dropInsertPublication);

        String insertSubmission = "CREATE PROCEDURE insert_submission\n" +
                "     (IN title varchar(200), IN doc_link varchar(200), IN email varchar(200))\n" +
                "BEGIN\n" +
                "    DECLARE s_id_val INT DEFAULT 1;\n" +
                "\n" +
                "    SELECT (max(s_id) + 1) INTO s_id_val\n" +
                "    FROM submission;\n" +
                "\n" +
                "    INSERT INTO submission\n" +
                "    VALUES(s_id_val, 0, title, doc_link, CURDATE(), email);\n" +
                "END";
        String insertPublication = "CREATE PROCEDURE insert_publication\n" +
                "     (IN p_id INT, IN title varchar(200), IN pages INT, IN doc_link varchar(200), IN s_id varchar(200))\n" +
                "BEGIN\n" +
                "    DECLARE p_id_val INT DEFAULT 1;\n" +
                "\n" +
                "    SELECT (max(p_id) + 1) INTO p_id_val\n" +
                "    FROM publication;\n" +
                "\n" +
                "    INSERT INTO submission\n" +
                "    VALUES(p_id_val, title, pages, CURDATE(), doc_link, 0, s_id);\n" +
                "END";

        execQuery(insertSubmission);
        execQuery(insertPublication);
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
