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
        //insertRows();

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
                "        Title varchar(500),\n" +
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
        //Customer insertions
        insertCustomers();
        //Account insertions
        insertAccounts();
        //Owns insertions
        insertOwns();
    }

    private static void insertCustomers() {
        String insertQuery = "INSERT INTO customer VALUES";
        String cemQuery ="('20000001', 'Cem', '1980.10.10', 'Engineer', 'Tunali', 'Ankara', 'TC')";
        String aslıQuery = "( '20000002', 'Asli', DATE '1985.09.08', 'Teacher', 'Nisantasi', 'Istanbul', 'TC')";
        String ahmetQuery = "( '20000003',  'Ahmet', DATE '1995.02.11.' , 'Salesman', 'Karsiyaka', 'Izmir',  'TC')";
        String johnQuery = "( '20000004', 'John', DATE '1990.04.16', 'Architect', 'Kizilay',  'Ankara', 'ABD')";

        execQuery( insertQuery + cemQuery);
        execQuery( insertQuery + aslıQuery);
        execQuery( insertQuery + ahmetQuery);
        execQuery( insertQuery + johnQuery);
        System.out.println("Insertions completed.");
    }


    private static void insertAccounts() {
        String insertQuery = "INSERT INTO account VALUES";
        String kızılayQuery = "('A0000001', 'Kizilay', '2000.00', '2009.01.01')";
        String bilkentQuery = "('A0000002', 'Bilkent', '8000.00', '2011.01.01')";
        String cankayaQuery = "('A0000003' ,'Cankaya' ,'4000.00' ,'2012.01.01')";
        String sincanQuery = "('A0000004' , 'Sincan' ,'1000.00' , '2012.01.01')";
        String tandoganQuery = "('A0000005' ,'Tandogan' ,'3000.00' ,'2013.01.01')";
        String eryamanQuery = "('A0000006' ,'Eryaman' ,'5000.00' ,'2015.01.01')";
        String umitkoyQuery = "('A0000007' ,'Umitkoy' ,'6000.00' ,'2017.01.01')";

        execQuery( insertQuery + kızılayQuery);
        execQuery( insertQuery + bilkentQuery);
        execQuery( insertQuery + cankayaQuery);
        execQuery( insertQuery + sincanQuery);
        execQuery( insertQuery + tandoganQuery);
        execQuery( insertQuery + eryamanQuery);
        execQuery( insertQuery + umitkoyQuery);
    }


    private static void insertOwns() {
        //TODO write insert queries f
        String insertQuery = "INSERT INTO owns VALUES";
        String[] ownQuery  = new String[10];
        ownQuery[0] = "( '20000001' ,'A0000001')";
        ownQuery[1] = "( '20000001' ,'A0000002')";
        ownQuery[2] = "( '20000001' ,'A0000003')";
        ownQuery[3] = "( '20000001' ,'A0000004')";
        ownQuery[4] = "( '20000002' ,'A0000002')";
        ownQuery[5] = "( '20000002' ,'A0000003')";
        ownQuery[6] = "( '20000002' ,'A0000005')";
        ownQuery[7] = "( '20000003' ,'A0000006')";
        ownQuery[8] = "( '20000003' ,'A0000007')";
        ownQuery[9] = "( '20000004' ,'A0000006')";

        for ( String own: ownQuery){
            execQuery( insertQuery + own);
        }
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
