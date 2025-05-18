<?php
//
//	Objet : Utilisateur
//	/backend/models/Utilisateur.php
//



class Utilisateur {
	private $pseudo;
	private $email;
	private $motdepasse;
	private $hashpass;
	private $role;

	// ======= SETTERS =======

	public function set_pseudo($texte) {
		$texte = trim($texte);
		if (strlen($texte) < 3 || strlen($texte) > 50) return false;
		if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $texte)) return false;
		$this->pseudo = $texte;
		return true;
	}

	public function set_email($texte) {
		$texte = trim($texte);
		if (strlen($texte) > 255) return false;
		if (!filter_var($texte, FILTER_VALIDATE_EMAIL)) return false;
		$this->email = $texte;
		return true;
	}

	public function set_password($texte) {
		if (strlen($texte) < 6) return false;
		$this->motdepasse = $texte;
		return true;
	}

	public function set_role($valeur) {
		$roles = ['passager', 'chauffeur', 'les_deux'];
		if (!in_array($valeur, $roles)) return false;
		$this->role = $valeur;
		return true;
	}

	// ======= GETTERS =======

	public function get_pseudo() {
		return $this->pseudo;
	}

	public function get_email() {
		return $this->email;
	}

	public function get_password() {
		return $this->motdepasse;
	}
	
	public function get_hashpass() {
		return $this->hashpass;
	}

	public function get_role() {
		return $this->role;
	}

	// ======= Database operations =======

	// Check if a user already exists by email
	public function is_user_exist(PDO $pdo) {
		$sql = "SELECT COUNT(*) FROM utilisateurs WHERE email = :email";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([':email' => $this->email]);
		return $stmt->fetchColumn() > 0;
	}
	
	// Check if a user already exists by pseudo
	public function is_pseudo_exist(PDO $pdo) {
		$sql = "SELECT COUNT(*) FROM utilisateurs WHERE pseudo = :pseudo";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([':pseudo' => $this->pseudo]);
		return $stmt->fetchColumn() > 0;
	}

	// Add the current user to the database
	public function add_user(PDO $pdo) {
		$sql = "INSERT INTO utilisateurs (pseudo, email, mot_de_passe, role) 
		        VALUES (:pseudo, :email, :motdepasse, :role)";
		$stmt = $pdo->prepare($sql);
		$hash = password_hash($this->motdepasse, PASSWORD_DEFAULT);
		return $stmt->execute([
			':pseudo'     => $this->pseudo,
			':email'      => $this->email,
			':motdepasse' => $hash,
			':role'       => $this->role
		]);
	}
	
	// Load a user by their email
	public function load_user_by_email(PDO $pdo) {
		$sql = "SELECT * FROM utilisateurs WHERE email = :email";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([':email' => $this->email]);
		$data = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($data) {
			$this->pseudo = $data['pseudo'];
			$this->hashpass = $data['mot_de_passe'];
			$this->role = $data['role'];
			return $data;
		}

		return false;
	}
}
?>
