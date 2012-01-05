<?php
// ===========================================================================================
//
// SQLCreateArticleTable.php
//
// SQL statements to create the tables for the Article tables.
//
// WARNING: Do not forget to check input variables for SQL injections. 
//
// Author: Mikael Roos
//


// Get the tablenames
$tArticle 		= DBT_Article;
$tTopic             = DBT_Topic;
$tTopic2Post    = DBT_Topic2Post;
$tUser 			= DBT_User;
$tGroup 		= DBT_Group;
$tGroupMember 	= DBT_GroupMember;
$tStatistics	= DBT_Statistics;

// Get the SP names
$spSPCreateNewArticle	= DBSP_SPCreateNewArticle;
$spSPDisplayArticle				= DBSP_SPDisplayArticle;
$spSPUpdateArticle			= DBSP_SPUpdateArticle;
$spSPListArticles			= DBSP_SPListArticles;
$spSPGetTopicList                                        = DBSP_SPGetTopicList;
$spSPGetTopicDetails                                    = DBSP_SPGetTopicDetails;
$spSPGetTopicDetailsAndPosts                    = DBSP_SPGetTopicDetailsAndPosts;
$spSPGetPostDetails                                    = DBSP_SPGetPostDetails;
$spSPInitialPostPublish                    = DBSP_SPInitialPostPublish;
$spSPInsertOrUpdatePost                            = DBSP_SPInsertOrUpdatePost;

// Get the UDF names
$udfFCheckUserIsOwnerOrAdmin2	= DBUDF_FCheckUserIsOwnerOrAdmin2;

// Get the trigger names
$trAddArticle					= DBTR_TAddArticle;
$trSubArticle					= DBTR_TSubArticle;

// Create the query
$query = <<<EOD
-- SET FOREIGN_KEY_CHECKS=0;
--
-- SP to insert article
--
DROP PROCEDURE IF EXISTS {$spSPCreateNewArticle};
CREATE PROCEDURE {$spSPCreateNewArticle}
(
    IN aUserId INT, 
    IN aTitle VARCHAR(100), 
    IN aText VARCHAR(500)
)
BEGIN
        INSERT INTO {$tArticle}    
            (Article_idUser, titleArticle, textArticle, dateArticle) 
            VALUES 
            (aUserId, aTitle, aText, NOW());
END;
--
-- SP to update article
--
DROP PROCEDURE IF EXISTS {$spSPUpdateArticle};
CREATE PROCEDURE {$spSPUpdateArticle}
(
    IN aArticleId INT, 
    IN aUserId INT, 
    IN aTitle VARCHAR(100), 
    IN aText VARCHAR(500)
)
BEGIN
        UPDATE {$tArticle} SET
            titleArticle     = aTitle,
            textArticle     = aText,
            dateArticle    = NOW()
        WHERE
            idArticle = aArticleId and
            $udfFCheckUserIsOwnerOrAdmin2(aArticleId,aUserId)
        LIMIT 1;
END;

--
-- SP to get the contents of an article
--
DROP PROCEDURE IF EXISTS {$spSPDisplayArticle};
CREATE PROCEDURE {$spSPDisplayArticle}
(
    IN aArticleId INT, 
    IN aUserId INT
)
BEGIN
    SELECT 
        A.titleArticle AS title,
        A.textArticle AS content,
        A.dateArticle AS mydate,
        U.accountUser AS username        
    FROM {$tArticle} AS A
        INNER JOIN {$tUser} AS U
            ON A.Article_idUser = U.idUser
    WHERE
        idArticle = aArticleId and
        $udfFCheckUserIsOwnerOrAdmin2(aArticleId,aUserId);

END;
--
-- SP to get the contents of an article and provide a list of the latest articles 
--
-- Limit does not accept a varible
-- http://bugs.mysql.com/bug.php?id=11918
--
DROP PROCEDURE IF EXISTS {$spSPListArticles};
CREATE PROCEDURE  {$spSPListArticles}
(
    IN aArticleId INT, 
    IN aUserId INT
)
BEGIN
    SELECT 
        idArticle AS id,
        titleArticle AS title,
        dateArticle AS mydate
    FROM {$tArticle}
    WHERE
        Article_idUser = aUserId  
    ORDER BY dateArticle
    LIMIT 20;
END;
--
--  Create UDF that checks if user owns article or is member of group adm.
--
DROP FUNCTION IF EXISTS {$udfFCheckUserIsOwnerOrAdmin2};
CREATE FUNCTION {$udfFCheckUserIsOwnerOrAdmin2}
(
    aArticleId INT,
    aUserId INT
)
RETURNS BOOLEAN
NOT DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE isAdmin INT;
    DECLARE isOwner INT;
    
    SELECT idUser INTO isOwner
    FROM {$tUser} AS U
        INNER JOIN {$tArticle} AS A
            ON U.idUser = A.Article_idUser
    WHERE
        idArticle = aArticleId AND
        idUser = aUserId;
    IF isOwner THEN
        RETURN TRUE;
    END IF;    
    SELECT idUser INTO isAdmin
    FROM {$tUser} AS U
        INNER JOIN {$tGroupMember} AS GM
            ON U.idUser = GM.GroupMember_idUser
        INNER JOIN {$tGroup} AS G
            ON G.idGroup = GM.GroupMember_idGroup
    WHERE
        idGroup = 'adm' AND
        idUser = aUserId;   
    RETURN isAdmin;        
END;
--
-- Create trigger for Statistics
-- Add +1 when new article is created
--
DROP TRIGGER IF EXISTS {$trAddArticle};
CREATE TRIGGER {$trAddArticle}
AFTER INSERT ON {$tArticle}
FOR EACH ROW
BEGIN
  UPDATE {$tStatistics} 
  SET 
      numOfArticlesStatistics = numOfArticlesStatistics + 1
  WHERE 
      Statistics_idUser = NEW.Article_idUser;
END;
--
-- Create trigger for Statistics
-- Sub -1 when new article is deleted
--
DROP TRIGGER IF EXISTS {$trSubArticle};
CREATE TRIGGER {$trSubArticle}
AFTER DELETE ON {$tArticle}
FOR EACH ROW
BEGIN
  UPDATE {$tStatistics} 
  SET 
      numOfArticlesStatistics = numOfArticlesStatistics - 1
  WHERE 
      Statistics_idUser = OLD.Article_idUser;
END;
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Table for Topic
--
-- A forum topic. To connect a topic to all its post, look in table Topic2Post. However, the
-- first post is stored in the topic, for convinience and reduce of joins when looking for the
-- title of the topic (which is stored in the initial post).
--
DROP TABLE IF EXISTS {$tTopic};
CREATE TABLE {$tTopic} (

    --
    -- Primary key(s)
    --
    idTopic INT AUTO_INCREMENT NOT NULL PRIMARY KEY,

    --
    -- Foreign keys
    --
    
    -- The first topic post
    Topic_idArticle INT NOT NULL,
    FOREIGN KEY (Topic_idArticle) REFERENCES {$tArticle}(idArticle),
    
    -- Last person who posted in this topic
    lastPostByTopic INT NOT NULL,
    FOREIGN KEY (lastPostByTopic) REFERENCES {$tUser}(idUser),
      
    --
    -- Attributes
    --
    
    -- Counts the numer of posts in this topic
    counterTopic INT NOT NULL,
    
    -- Last time for posting to this topic
    lastPostWhenTopic DATETIME NOT NULL

);


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Table for Topic2Post
--
-- Connection between topics and posts. 
--
DROP TABLE IF EXISTS {$tTopic2Post};
CREATE TABLE {$tTopic2Post} (

    --
    -- Primary key(s)
    --
    -- Se below, combined from the two foreign keys

    --
    -- Foreign keys
    --
    
    -- The Topic
    Topic2Post_idTopic INT NOT NULL,
    FOREIGN KEY (Topic2Post_idTopic) REFERENCES {$tTopic}(idTopic),
  
    -- The Post
    Topic2Post_idArticle INT NOT NULL,
    FOREIGN KEY (Topic2Post_idArticle) REFERENCES {$tArticle}(idArticle),

    -- Primary key(s)
    PRIMARY KEY (Topic2Post_idTopic, Topic2Post_idArticle)

    --
    -- Attributes
    --
    -- No additional attributes
    
);
-- SET FOREIGN_KEY_CHECKS=1;

-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to get a list of all the topics together with details on each topic.
--
DROP PROCEDURE IF EXISTS {$spSPGetTopicList};
CREATE PROCEDURE {$spSPGetTopicList} ()
BEGIN
    SELECT 
        T.idTopic AS topicid,
        T.counterTopic AS postcounter,
        T.lastPostWhenTopic AS latest,
        A.idArticle AS postid,
        A.titleArticle AS title,
        A.dateArticle AS mydate,
        U.idUser AS userid,
        U.accountUser AS username,
        U1.accountUser AS latestby
    FROM {$tTopic} AS T
        INNER JOIN {$tArticle} AS A
            ON T.Topic_idArticle = A.idArticle
        INNER JOIN {$tUser} AS U
            ON A.Article_idUser = U.idUser
        INNER JOIN {$tUser} AS U1
            ON T.lastPostByTopic = U1.idUser 
    ORDER BY lastPostWhenTopic DESC
    ;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to get details of a topic.
--
-- If aTopicId is set, use that.
-- If not, try to find a topic using the aPostId, as a second way to find the topic details.
--
DROP PROCEDURE IF EXISTS {$spSPGetTopicDetails};
CREATE PROCEDURE {$spSPGetTopicDetails}
(
    IN aTopicId INT,
    IN aPostId INT
)
BEGIN
    IF aTopicId = 0 THEN
    BEGIN
        SELECT Topic2Post_idTopic INTO aTopicId FROM {$tTopic2Post} WHERE Topic2Post_idArticle = aPostId;
    END;
    END IF;
    
    --
    -- Get the topic details
    --
    SELECT 
        T.idTopic AS topicid,
        T.counterTopic AS postcounter,
        T.lastPostWhenTopic AS lastpostwhen,
        T.Topic_idArticle AS toppost,
        A.titleArticle AS title,
        A.dateArticle AS mydate,
        U.accountUser AS creator,        
        U.idUser AS creatorid,
        U1.accountUser AS lastpostby
    FROM {$tTopic} AS T
        INNER JOIN {$tArticle} AS A
            ON T.Topic_idArticle = A.idArticle
        INNER JOIN {$tUser} AS U
            ON A.Article_idUser = U.idUser
        INNER JOIN {$tUser} AS U1
            ON T.lastPostByTopic = U1.idUser
    WHERE
        T.idTopic = aTopicId
    ;
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to get the content of a topic, both topic details and all the posts related to the topic.
--
DROP PROCEDURE IF EXISTS {$spSPGetTopicDetailsAndPosts};
CREATE PROCEDURE {$spSPGetTopicDetailsAndPosts}
(
    IN aTopicId INT
)
BEGIN
    --
    -- Get the topic details
    --
    CALL {$spSPGetTopicDetails}(aTopicId, 0);
    
    --
    -- Get the list of all posts related to this topic
    --
    SELECT
        A.idArticle AS postid,
        A.titleArticle AS title,
        A.textArticle AS text,
        A.dateArticle AS mydate,
        U.idUser AS userid,
        U.accountUser AS username,
        U.avatarUser AS avatar,
        U.gravatarUser AS gravatar
    FROM {$tTopic2Post} AS T2P
        INNER JOIN {$tArticle} AS A
            ON A.idArticle = T2P.Topic2Post_idArticle
        INNER JOIN {$tUser} AS U
            ON A.Article_idUser = U.idUser
    WHERE 
        T2P.Topic2Post_idTopic = aTopicId AND
        A.publishedArticle IS NOT NULL
    ORDER BY dateArticle ASC
    ;
    
END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to get the details of a topic and a specific post.
--
DROP PROCEDURE IF EXISTS {$spSPGetPostDetails};
CREATE PROCEDURE {$spSPGetPostDetails}
(
    IN aPostId INT
)
BEGIN
    --
    -- Get the post details
    --
    SELECT
        A.idArticle AS postid,
        A.titleArticle AS title,
        A.textArticle AS text,
        A.dateArticle AS mydate,
        IF(publishedArticle IS NULL, 0, 1) AS isPublished,
        IF(draftModifiedArticle IS NULL, 0, 1) AS hasDraft,
        A.draftTitleArticle AS draftTitle,
        A.draftTextArticle AS draftText,
        A.draftModifiedArticle AS draftModified
    FROM {$tTopic2Post} AS T2P
        INNER JOIN {$tArticle} AS A
            ON A.idArticle = T2P.Topic2Post_idArticle
        INNER JOIN {$tUser} AS U
            ON A.Article_idUser = U.idUser
    WHERE 
        A.idArticle = aPostId AND
        A.publishedArticle IS NOT NULL
    ORDER BY dateArticle ASC
    ;
    
END;
-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP for the first time when the post is published. Create/update topic tables accordingly.
--
-- If aTopicId is 0 then insert new entry into topic-table.
-- Keep tables Topic and Topic2Post updated.
--
DROP PROCEDURE IF EXISTS {$spSPInitialPostPublish};
CREATE PROCEDURE {$spSPInitialPostPublish}
(
    INOUT aTopicId INT,
    IN aPostId INT,
    IN aUserId INT
)
BEGIN    
    --
    -- Is it a new topic? Then create the topic else update it.
    --
    IF aTopicId = 0 THEN
    BEGIN
        INSERT INTO {$tTopic}    
            (Topic_idArticle, counterTopic, lastPostWhenTopic, lastPostByTopic) 
            VALUES 
            (aPostId, 1, NOW(), aUserId);
            SET aTopicId = LAST_INSERT_ID();
    END;
    
    --
    -- Topic exists, just update it
    --
    ELSE
    BEGIN
        UPDATE {$tTopic} SET
            counterTopic             = counterTopic + 1,
            lastPostWhenTopic = NOW(), 
            lastPostByTopic        = aUserId
        WHERE 
            idTopic = aTopicId
        LIMIT 1;
    END;
    END IF;

    --
    -- First time this post is published, insert post entry in Topic2Post
    --
    INSERT INTO {$tTopic2Post}    
        (Topic2Post_idTopic, Topic2Post_idArticle) 
        VALUES 
        (aTopicId, aPostId);
END;

-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- SP to insert or update a forum post.
--
-- If aPostId is 0 then insert a new post.
-- else update the post.
-- Save 'draft' or 'publish' the post depending on aAction.
-- A post must be published once before it can be viewed. 
--
DROP PROCEDURE IF EXISTS {$spSPInsertOrUpdatePost};
CREATE PROCEDURE {$spSPInsertOrUpdatePost}
(
    INOUT aPostId INT,
    INOUT aTopicId INT,
    OUT isPublished INT,
    OUT hasDraft INT,
    IN aUserId INT, 
    IN aTitle VARCHAR(100), 
    IN aContent VARCHAR(500),
    IN aAction CHAR(7) -- 'draft' or 'publish'
)
BEGIN
    DECLARE isPostPublished BOOLEAN;
    
    --
    -- First see if this is a completely new post, if it is, start by creating an empty post
    --
    IF aPostId = 0 THEN
    BEGIN
        INSERT INTO {$tArticle}    (Article_idUser, dateArticle) VALUES (aUserId, NOW());
        SET aPostId = LAST_INSERT_ID();
    END;
    END IF;

    --
    -- Are we just saving a draft?
    --
    IF aAction = 'draft' THEN
    BEGIN
        UPDATE {$tArticle} SET
            draftTitleArticle         = aTitle,
            draftTextArticle     = aContent,
            draftModifiedArticle    = NOW()
        WHERE
            idArticle = aPostId  AND
            {$udfFCheckUserIsOwnerOrAdmin2}(aPostId, aUserId)
        LIMIT 1;
    END;

    --
    -- Or are we publishing the post? Then prepare it and remove the draft.
    --
    ELSEIF aAction = 'publish' THEN
    BEGIN
        --
        -- Before we proceed, lets see if this post is published or not. 
        --
        SELECT publishedArticle INTO isPostPublished FROM {$tArticle} WHERE idArticle = aPostId;

        --
        -- Need to do some extra work if this is the first time the post is published
        --
        IF isPostPublished IS NULL THEN
        BEGIN
            CALL {$spSPInitialPostPublish}(aTopicId, aPostId, aUserId);
        END;
        END IF;
        
        --
        -- Re-publish the post it and remove the draft.
        --
        UPDATE {$tArticle} SET
            titleArticle                     = aTitle,
            textArticle                 = aContent,
            dateArticle                = NOW(),
            publishedArticle            = NOW(),
            draftTitleArticle         = NULL,
            draftTextArticle     = NULL,
            draftModifiedArticle    = NULL
        WHERE
            idArticle = aPostId  AND
            {$udfFCheckUserIsOwnerOrAdmin2}(aPostId, aUserId)
        LIMIT 1;    

    END;
    END IF;

    --
    -- Check some status issues, return as OUT parameters, might be useful in the GUI.
    --
    SELECT 
        IF(publishedArticle IS NULL, 0, 1),
        IF(draftModifiedArticle IS NULL, 0, 1)
        INTO 
        isPublished,
        hasDraft
    FROM {$tArticle} 
    WHERE 
        idArticle = aPostId 
    ;

END;


-- +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
--
-- Insert some default topics
--
SET @action='publish';
SET @post=0;
SET @topic=0;
CALL {$spSPInsertOrUpdatePost} (@post, @topic, @notUsed, @notUsed, 1, 'Rome was not built in one day', 'At least, that is the common opinion.', @action);

SET @post=0;
CALL {$spSPInsertOrUpdatePost} (@post,  @topic, @notUsed, @notUsed, 2, '', 'But you never now. I have heard otherwise.', @action);

SET @post=0;
SET @topic=0;
CALL {$spSPInsertOrUpdatePost} (@post,  @topic, @notUsed, @notUsed, 2, 'A forum should be open for all', 'Everybody should be able to say what they feel.', @action);

SET @post=0;
CALL {$spSPInsertOrUpdatePost} (@post,  @topic, @notUsed, @notUsed, 1, '', 'Is this really your opinion!!?', @action);

SET @post=0;
CALL {$spSPInsertOrUpdatePost} (@post,  @topic, @notUsed, @notUsed, 2, '', 'No, just said it for the fun of it.', @action);

SET @post=0;
SET @topic=0;
CALL {$spSPInsertOrUpdatePost} (@post,  @topic, @notUsed, @notUsed, 1, 'Which is the best forum ever?', 'I really would like to know your opinion on this matter.', @action);


EOD;




?>