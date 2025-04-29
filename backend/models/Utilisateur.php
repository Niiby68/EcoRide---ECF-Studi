<?php
//
//	Objet : Utilisateur
//
//	/backend/models/Utilisateur.php
//
class Utilisateur {
	private $pseudo;
	private $email;
	private $motdepasse;
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

	public function get_role() {
		return $this->role;
	}

	// ======= Database operations =======

	// Check if a user already exists by email
	public function is_user_exist(PDO $pdo) {
		$sql = "SELECT COUNT(*) FROM utilisateurs WHERE email = ?";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([$this->email]);
		return $stmt->fetchColumn() > 0;
	}

	// Add the current user to the database
	public function add_user(PDO $pdo) {
		$sql = "INSERT INTO utilisateurs (pseudo, email, mot_de_passe, role) VALUES (?, ?, ?, ?)";
		$stmt = $pdo->prepare($sql);
		$hash = password_hash($this->motdepasse, PASSWORD_DEFAULT);
		return $stmt->execute([$this->pseudo, $this->email, $hash, $this->role]);
	}
}
