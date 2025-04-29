<?php


class Company
{
    public $name_company;
    public $password;
    public $confirm_password;
    public $vat_number;
    public $telephone;
    public $province;
    public $city;
    public $address;
    public $cap;
    public $email;
    public $birth_of_day;


    public function __construct($name_company, $password, $confirm_password, $vat_number, $telephone, $province, $city, $address, $cap, $email, $birth_of_day)
    {
        $this->name_company = htmlspecialchars($name_company);
        $this->password = htmlspecialchars($password);
        $this->confirm_password = htmlspecialchars($confirm_password);
        $this->vat_number = htmlspecialchars($vat_number);
        $this->telephone = htmlspecialchars($telephone);
        $this->province = htmlspecialchars($province);
        $this->city = htmlspecialchars($city);
        $this->address = htmlspecialchars($address);
        $this->cap = htmlspecialchars($cap);
        $this->email = htmlspecialchars($email);
        $this->birth_of_day = htmlspecialchars($birth_of_day);
    }

    public function save($connection)
    {
        // Hash the password using md5
        $hashedPassword = md5($this->password);

        // Prepare the SQL statement to insert the company data into the database
        $stmt = $connection->prepare("INSERT INTO companies (name_company, password, vat_number, telephone, email, birth_of_day, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $this->name_company, $hashedPassword, $this->vat_number, $this->telephone, $this->email, $this->birth_of_day, $this->address);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public static function all($connection)
    {
        // Query to select all records from the company table
        $query = "SELECT * FROM companies";

        // Execute the query and return the result
        return $connection->query($query);
    }
}
