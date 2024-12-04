<?php
class User
{
    private $ID;
    public $Name;
    public $start;
    public $gps_x;
    public $gps_y;
    public $lenght;
    public $duration;
    public $geo;
    public $min_distance;

    public function __construct($ID)
    {
        $this->ID = $ID;
        $this->load_user();
    }
    private function load_user()
    {
        $user = Db::queryOne("SELECT * FROM fluffcon_users WHERE ID = $this->ID");
        $this->Name = $user['Name'];
        $this->gps_x = $user['Gps_x'];
        $this->gps_y = $user['Gps_y'];
        $this->start = $user['Time'];
        $this->lenght = $user['Path_lenght'];
        $this->duration = $user['Driving_time'];
    }
    public function __toString()
    {
        // $this->min_distance = sqrt($this->min_distance);
        $user = [
            'ID' => $this->ID,
            'Name' => $this->Name,
            // 'Gps_x' => $this->gps_x,
            // 'Gps_y' => $this->gps_y,
            'Time' => $this->start,
            'Path_lenght' => $this->lenght,
            'Driving_time' => $this->duration,
        ];
        return json_encode($user, JSON_UNESCAPED_UNICODE);
    }

    public function goeJSON()
    {
        $this->geo = [
            "length" => $this->lenght,
            "duration" => $this->duration,
            "geometry" => [
                "type" => "Feature",
                "geometry" => [
                    "type" => "LineString",
                    "coordinates" => [
                        // [$this->gps_x, $this->gps_y]
                    ]
                ],
                "properties" => []
            ]
        ];
        $this->get_route();
        $json = json_encode($this->geo, JSON_UNESCAPED_UNICODE);
        $json = str_replace(':[]', ':{}', $json);
        return $json;
    }
    private function get_route()
    {
        $sql = "SELECT * FROM fluffcon_routes WHERE User_ID = $this->ID order by ID asc";
        $routes = Db::queryAll($sql);
        // print_r($routes);
        foreach ($routes as $route) {
            // print_r($route);
            // echo "<br>";
            // print_r(json_encode($this->geo));
            // echo "<br>";
            $this->geo['geometry']['geometry']['coordinates'][] = [floatval($route['Gps_x']), floatval($route['Gps_y'])];
            
        }
    }
    public function set_distance($distance)
    {
        $this->min_distance = $distance;
    }

    public static function create($name, $gps_x, $gps_y, $route, $time)
    {
        self::find_user($name);
        // $route = json_decode($route, true);
        // print_r( $route);

        // $time = strtotime($time);
        $gps_x = floatval($gps_x);
        $gps_y = floatval($gps_y);
        $timestamp = date('Y-m-d H:i:s', strtotime($time));
        if (!isset($route["duration"])) {
            $route = $route[0];
        }
        $driving_time = $route["duration"];
        $path_lenght = $route["length"];
        
        $sql = "INSERT INTO fluffcon_users (Name, Time, Gps_x, Gps_y, Driving_time, Path_lenght) VALUES ('$name','$timestamp', $gps_x, $gps_y, $driving_time, $path_lenght)";
        // echo $sql;
        Db::query($sql);
        $ID = Db::queryOne("SELECT ID FROM fluffcon_users WHERE Name = '$name'")['ID'];
        
        // print_r($route["geometry"]["geometry"] );
        foreach ($route["geometry"]["geometry"]["coordinates"] as $cordinates) {
                // print_r($point);
                $sql = "INSERT INTO fluffcon_routes (User_ID, Gps_x, Gps_y) VALUES ($ID, $cordinates[0], $cordinates[1])";
                // echo $sql;
                Db::query($sql);
            }
        
        return new User($ID);
    }

    private static function find_user($name)
    {
        $sql = "SELECT * FROM fluffcon_users WHERE Name = '$name'";
        $user = Db::queryOne($sql);
        if ($user) {
            $sql = "DELETE FROM fluffcon_routes WHERE User_ID = $user[ID]";
            Db::query($sql);
            $sql = "DELETE FROM fluffcon_users WHERE ID = $user[ID]";
            Db::query($sql);
        }
        return;
    }
}