<?php
class Find_users
{
    private $gps_x;
    private $gps_y;
    private $users;
    private $count;

    public function __construct($gps_x, $gps_y)
    {
        $this->gps_x = $gps_x;
        $this->gps_y = $gps_y;
        $this->load_users();
    }
    public function get_users()
    {
        $users = Db::query("SELECT * FROM users");
        return $users;
    }
    public function return_users()
    {
        $users = "[";
        $i = 0;
        while ($i < count($this->users)) {
            $users .= $this->users[$i] . ",";
            // print_r($this->users[$i]);
            // echo "<br>";
            $i++;
        }
        $users = substr($users, 0, -1);
        $users .= "]";
        return $users;
    }
    private function load_users()
    {
        $x = $this->gps_x;
        $y = $this->gps_y;

        $sql = "SELECT User_ID,Gps_x,Gps_y, MIN(POWER(Gps_x - $x, 2) + POWER(Gps_y - $y, 2)) AS min_distance FROM fluffcon_routes GROUP BY User_ID ORDER BY min_distance ASC";
        // echo $sql;
        $routes = Db::queryAll($sql);
        // echo print_r($routes);
        $i = 0;
        foreach ($routes as $route) {
            $user = new User($route['User_ID']);
            // print_r($route);
            // echo "<br>";
            // $user->set_distance($route['min_distance']);
            $this->users[$i] = $user;
            $i++;
        }
        // echo "<br>";

    }
}