<!DOCTYPE html>

<html>
<nav class="navtop">
    <div>
        <div>

        <!-- Search Form -->
        <h1><a href="home.php">Fresh Tomatoes</a></h1>
            <form action="search.php" method="get" class="searchbar">
                <input type="text" name="search_query" placeholder="Search for..." class="search-input">
                    <select name="search_type" class="search-type">
                    <option value="name">Movie Name</option>
                    <option value="director">Director</option>
                    <option value="genre">Genre</option>
                    </select>
                <input type="submit" value="Search" class="search-button">
            </form>
        </div>

				<a href="people-search.php"><i class="fas fa-user-circle"></i>Search Users</a>
        <a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>
</nav>

</html>

