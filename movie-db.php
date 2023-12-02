<?php

// New Functions 

function getMovieByName($name) {
  global $db;
  $query = "select * from Movie where name= :name";

  $statement = $db->prepare($query); 
  $statement->bindValue(':name', $name);
  $statement->execute();
  $results = $statement->fetchAll();   // fetch()
  $statement->closeCursor();
  return $results;
}

function getAllComments($name) {
  global $db;
  $query = "select rating, review_time, review_text 
  from Review 
  join Has on Review.reviewId = Has.reviewId
  where Has.name=:name";

  $statement = $db->prepare($query); 
  $statement->bindValue(':name', $name);
  $statement->execute();
  $reviews = $statement->fetchAll();   // fetch()
  $statement->closeCursor();
  return $reviews;
}

// function followUser($followerUserId, $followedUserId) 
// {
//   global $db; 

//   // good way
//   $query = "insert into Follows(user1_userId, user2_userId) values (:followerUserId, :followedUserId)";

//   $statement = $db->prepare($query); 
//   $statement->bindValue(':followerUserId', $followerUserId);
//   $statement->bindValue(':followedUserId', $followedUserId);
//   $statement->execute();
//   $statement->closeCursor();
// }

// function getUsersFollowed($userId) {
//   global $db;
//   $query = "select bio
//   from Users
//   join Follows on Users.UserId = Follows.user2_userId
//   where Follows.user1_userId:=userId";

//   $statement = $db->prepare($query);
//   $statement->bindValue(':userId', $userId); 
//   $statement->execute();  
//   $results = $statement->fetchAll();   // fetch()
//   $statement->closeCursor();
//   return $results;
// }

?>
