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

        //Create tables
        createTables();

        //Insertions
        insertRows();

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
        String tableNames[] = { "authorExpertise", "reviewerExpertise", "reviews", "editorPublisher",
                "cites", "invites",  "finances" , "sponsor",
                "publication", "conference","journal", "expertise", "submits", "author","reviewer",
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
                "        cnf_topic varchar(200),\n" +
                "                p_name varchar(200) PRIMARY KEY,\n" +
                "        FOREIGN KEY(p_name) REFERENCES publisher(p_name) ON DELETE CASCADE ON UPDATE CASCADE)\n" +
                "        ENGINE = INNODB";

        String journal = "CREATE TABLE journal(\n" +
                "        volume decimal(5,2),\n" +
                "        journal_topic varchar(200),\n" +
                "                p_name varchar(200) PRIMARY KEY,\n" +
                "        FOREIGN KEY (p_name) REFERENCES publisher(p_name) ON DELETE CASCADE ON UPDATE CASCADE)\n" +
                "        ENGINE = INNODB";

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
        System.out.println("Tables created");
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
    }

    private static void insertSubscribers() {
        String insertQuery = "INSERT INTO subscriber VALUES";
        String query1 = "('email1', 'institution1', 'password1', 'name1', 'surname1', '1')";
        String query2 = "('email2', 'institution2', 'password2', 'name2', 'surname2', '1')";
        String query3 = "('email3', 'institution3', 'password3', 'name3', 'surname3', '2')";
        String query4 = "('email4', 'institution4', 'password4', 'name4', 'surname4', '2')";
        String query5 = "('email5', 'institution5', 'password5', 'name5', 'surname5', '3')";
        String query6 = "('email6', 'institution6', 'password6', 'name6', 'surname6', '3')";


        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
        System.out.println("Publication insertions completed.");
    }

    private static void insertConference() {
        String insertQuery = "INSERT INTO conference VALUES";
        String query1 = "('1980.01.01', 'topic1', 'publisher1')";
        String query2 = "('1980.01.02', 'topic2', 'publisher2')";
        String query3 = "('1980.01.03', 'topic3', 'publisher3')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        System.out.println("Publication insertions completed.");
    }


    private static void insertExpertises() {
        String insertQuery = "INSERT INTO expertise VALUES";
        String query1 = "('expertise1')";
        String query2 = "('expertise2')";
        String query3 = "('expertise3')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        System.out.println("Publication insertions completed.");
    }

    private static void insertJournals() {
        String insertQuery = "INSERT INTO journal VALUES";
        String query1 = "('1', 'topic1', 'publisher4')";
        String query2 = "('2', 'topic2', 'publisher5')";
        String query3 = "('3', 'topic3', 'publisher6')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        System.out.println("Publication insertions completed.");
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
        System.out.println("Publication insertions completed.");
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
        System.out.println("Publication insertions completed.");
    }

    private static void insertInvites() {
        String insertQuery = "INSERT INTO invites VALUES";
        String query1 = "('email5', 'email3', '4', '0')";
        String query2 = "('email5', 'email4', '5', '1')";
        String query3 = "('email6', 'email3', '6', '2')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        System.out.println("Publication insertions completed.");
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
        System.out.println("Publication insertions completed.");
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
        System.out.println("Publication insertions completed.");
    }

    private static void insertAuthors() {
        String insertQuery = "INSERT INTO author VALUES";
        String query1 ="('email1')";
        String query2 ="('email2')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        System.out.println("Publisher insertions completed.");
    }

    private static void insertAuthorExpertises() {
        String insertQuery = "INSERT INTO authorExpertise VALUES";
        String query1 ="('email1', 'expertise1')";
        String query2 ="('email2', 'expertise2')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        System.out.println("Publisher insertions completed.");
    }

    private static void insertReviewerExpertises() {
        String insertQuery = "INSERT INTO reviewerExpertise VALUES";
        String query1 ="('email5', 'expertise1')";
        String query2 ="('email6', 'expertise2')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        System.out.println("Publisher insertions completed.");
    }

    private static void insertReviewers() {
        String insertQuery = "INSERT INTO reviewer VALUES";
        String query1 ="('email5')";
        String query2 ="('email6')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        System.out.println("Publisher insertions completed.");
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
        System.out.println("Publisher insertions completed.");
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
        System.out.println("Publisher insertions completed.");
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
        System.out.println("Publisher insertions completed.");
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
        System.out.println("Publisher insertions completed.");
    }

    private static void insertEditors() {
        String insertQuery = "INSERT INTO editor VALUES";
        String query1 ="('1', 'email3')";
        String query2 ="('2', 'email4')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        System.out.println("Publisher insertions completed.");
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
        String query4 ="('email2', '4', 'publisher1')";
        String query5 ="('email1', '5', 'publisher2')";
        String query6 ="('email2', '6', 'publisher3')";

        execQuery( insertQuery + query1);
        execQuery( insertQuery + query2);
        execQuery( insertQuery + query3);
        execQuery( insertQuery + query4);
        execQuery( insertQuery + query5);
        execQuery( insertQuery + query6);
        System.out.println("Publisher insertions completed.");
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
