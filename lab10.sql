
delimiter //

CREATE TABLE IF NOT EXISTS users
(
    user_id VARCHAR(30) NOT NULL,
    PRIMARY KEY (user_id)
) //

CREATE TABLE IF NOT EXISTS posts
(
    post_id INT NOT NULL AUTO_INCREMENT,
    content TEXT NOT NULL,
    author_id VARCHAR(30) NOT NULL,
    PRIMARY KEY (post_id),
    INDEX ( author_id ),
    FOREIGN KEY (author_id)
        REFERENCES users(user_id)
) //

-- ADD USER
DROP PROCEDURE adduser;
CREATE PROCEDURE 
    adduser ( IN arguser VARCHAR (30) ) 
LANGUAGE SQL
MODIFIES SQL DATA
BEGIN 
    INSERT INTO users ( user_id ) 
    VALUES ( arguser );
END//
-- VIEW USERS
    -- Note: no "view user" as there is no data to fetch if you already 
    -- know the id of the user
DROP PROCEDURE viewusers;
CREATE PROCEDURE 
    viewusers () 
LANGUAGE SQL
READS SQL DATA
BEGIN 
    SELECT * 
    FROM users
    ORDER BY users.user_id;
END// 


-- ADD POST
DROP PROCEDURE addpost;
CREATE PROCEDURE 
    addpost ( 
        IN arguser VARCHAR (30),
        IN argpost TEXT
)
LANGUAGE SQL
MODIFIES SQL DATA
BEGIN 
    INSERT INTO posts ( content, author_id ) 
    VALUES ( argpost, arguser );
END//
-- REMOVE POST
DROP PROCEDURE removepost;
CREATE PROCEDURE 
    removepost ( 
        IN argpost_id INT
    )
LANGUAGE SQL
MODIFIES SQL DATA
BEGIN 
    DELETE FROM posts  
    WHERE posts.post_id = argpost_id;
END//
-- READ POSTS FROM USER
DROP PROCEDURE viewuserposts;
CREATE PROCEDURE 
    viewuserposts ( 
        IN arguser_id VARCHAR (30) NOT NULL
    )
LANGUAGE SQL
READS SQL DATA
BEGIN 
    SELECT * FROM posts   
    WHERE posts.author_id = arguser_id
    ORDER BY posts.post_id;
END//

delimiter ;
