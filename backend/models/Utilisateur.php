<?php
declare(strict_types=1);

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
	http_response_code(403);
	exit('Accès interdit.');
}



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
	private $photo;



	// ======= SETTERS =======

	public function set_pseudo(string $texte): bool {
		$texte = trim($texte);
		if (strlen($texte) < 3 || strlen($texte) > 50) return false;
		if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $texte)) return false;
		$this->pseudo = $texte;
		return true;
	}

	public function set_email(string $texte): bool {
		$texte = trim($texte);
		if (strlen($texte) > 255) return false;
		if (!filter_var($texte, FILTER_VALIDATE_EMAIL)) return false;
		$this->email = $texte;
		return true;
	}

	public function set_password(string $texte): bool {
		if (strlen($texte) < 6) return false;
		$this->motdepasse = $texte;
		return true;
	}

	public function set_role(string $valeur): bool {
		$roles = ['passager', 'chauffeur', 'les_deux'];
		if (!in_array($valeur, $roles)) return false;
		$this->role = $valeur;
		return true;
	}

	public function set_photo(string $fichier): bool {
		if (strlen($fichier) > 100) return false;
		$this->photo = trim($fichier);
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

	public function get_photo() {
		return $this->photo;
	}



	// ======= Opérations sur la BDD =======

	// Vérifie si l'email de l'utilisateur est déjà utilisé
	public function is_user_exist(PDO $pdo): bool {
		$sql = "SELECT COUNT(*) FROM utilisateurs WHERE email = :email";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([':email' => $this->email]);
		return $stmt->fetchColumn() > 0;
	}
	
	// Vérifie si le pseudo de l'utilisateur est déjà utilisé
	public function is_pseudo_exist(PDO $pdo): bool {
		$sql = "SELECT COUNT(*) FROM utilisateurs WHERE pseudo = :pseudo";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([':pseudo' => $this->pseudo]);
		return $stmt->fetchColumn() > 0;
	}

	// Ajoute l'utilisateur dans la BDD
	public function add_user(PDO $pdo): bool {
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

	// Charge les données de l'utilisateur en utilisant son email
	public function load_user_by_email(PDO $pdo) {
		$sql = "SELECT * FROM utilisateurs WHERE email = :email";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([':email' => $this->email]);
		$data = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($data) {
			$this->pseudo    = $data['pseudo'];
			$this->hashpass  = $data['mot_de_passe'];
			$this->role      = $data['role'];
			$this->photo     = $data['photo'] ?? null;
			return $data;
		}

		return false;
	}

	// Met à jour la photo de l'utilisateur dans la BDD
	public function update_photo(PDO $pdo): bool {
		$sql = "UPDATE utilisateurs SET photo = :photo WHERE email = :email";
		$stmt = $pdo->prepare($sql);
		return $stmt->execute([
			':photo' => $this->photo,
			':email' => $this->email
		]);
	}
}
?>
