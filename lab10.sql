delimiter //

USE a637m351;

CREATE OR REPLACE TABLE a637m351.users(
    user_id CHAR(30) NOT NULL,
    PRIMARY KEY (user_id)
);
    -- ADD USER
    CREATE OR REPLACE PROCEDURE 
        a637m351.adduser ( IN arguser CHAR (30) ) 
    MODIFIES SQL DATA
    BEGIN 
    INSERT INTO a637m351.users ( user_id ) 
    VALUES ( arguser );
    END//
    -- REMOVE USER
    CREATE OR REPLACE PROCEDURE 
        a637m351.removeuser ( IN arguser CHAR (30) ) 
    MODIFIES SQL DATA
    BEGIN 
    DELETE FROM a637m351.users
    WHERE  a637m351.users.user_id = arguser;
        -- note: deletion cascades to posts table via foreign key
    END//
    -- VIEW USERS
        -- Note: no "view user" as there is no data to fetch if you already 
        -- know the id of the user
    CREATE OR REPLACE PROCEDURE 
        a637m351.viewusers () 
    READS SQL DATA
    BEGIN 
    SELECT * 
    FROM a637m351.users
    ORDER BY a637m351.users.user_id;
    END// 

CREATE OR REPLACE TABLE a637m351.posts(
    post_id INT NOT NULL AUTO_INCREMENT,
    content TEXT NOT NULL,
    author_id CHAR(30) NOT NULL,
    PRIMARY KEY (post_id),
    INDEX ( author_id ),
    FOREIGN KEY (author_id)
        REFERENCES users(user_id)
        ON UPDATE OR DELETE CASCADE
);
    -- ADD POST
    CREATE OR REPLACE PROCEDURE 
        a637m351.addpost ( 
            IN arguser CHAR (30),
            IN argpost TEXT,
    )
    MODIFIES SQL DATA
    BEGIN 
    INSERT INTO a637m351.posts ( content, author_id ) 
    VALUES ( argpost, arguser );
    END//
    -- REMOVE POST
    CREATE OR REPLACE PROCEDURE 
        a637m351.removepost ( 
            IN argpost_id INT NOT NULL
    )
    MODIFIES SQL DATA
    BEGIN 
    DELETE FROM a637m351.posts  
    WHERE  a637m351.posts.post_id = argpost_id;
    END//
    -- READ POST

delimiter ;