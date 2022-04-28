
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
-- READ POST

delimiter ;
