<?php
declare(strict_types=1);

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
	http_response_code(403);
	exit('Accès interdit.');
}



//
//	Objet : Utilisateur
//	Chemin : /backend/models/Utilisateur.php
//



class Utilisateur {
	private ?int $id = null;
	private ?string $pseudo = null;
	private ?string $email = null;
	private ?string $motdepasse = null;
	private ?string $photo = null;
	private ?int $credits = null;
	private ?string $date_inscription = null;
	private ?int $actif = null;
	
	private const DEFAULT_PHOTO   = 'defaut.png';
	private const DEFAULT_CREDITS = 20;
	private const STATUS_ACTIVE   = 1;



	// ======= SETTERS =======

	public function set_id(int $id): bool {
		$this->id = $id;
		return true;
	}

	public function set_pseudo(string $texte): bool {
		$texte = trim($texte);
		if (strlen($texte) < 3 || strlen($texte) > 50) return false;
		if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $texte)) return false;
		$this->pseudo = $texte;
		return true;
	}

	public function set_email(string $texte): bool {
		$texte = trim($texte);
		if (strlen($texte) > 100) return false;
		if (!filter_var($texte, FILTER_VALIDATE_EMAIL)) return false;
		$this->email = $texte;
		return true;
	}

	public function set_motdepasse(string $texte): bool {
		if (strlen($texte) < 6) return false;
		$this->motdepasse = password_hash($texte, PASSWORD_DEFAULT);
		return true;
	}

	public function set_photo(string $fichier): bool {
		$fichier = trim(basename($fichier));
		if ($fichier === '' || strlen($fichier) > 100) return false;
		$this->photo = $fichier;
		return true;
	}
	
	public function set_credits(int $valeur): bool {
		if ($valeur < 0) return false;
		$this->credits = $valeur;
		return true;
	}

	public function set_actif(int $statut): bool {
		if (!in_array($statut, [0, 1], true)) return false;
		$this->actif = $statut;
		return true;
	}



	// ======= GETTERS =======

	public function get_id(): ?int {
		return $this->id;
	}

	public function get_pseudo(): ?string {
		return $this->pseudo;
	}

	public function get_email(): ?string {
		return $this->email;
	}

	public function get_motdepasse(): ?string {
		return $this->motdepasse;
	}

	public function get_photo(): ?string {
		return $this->photo;
	}
	
	public function get_credits(): ?int {
		return $this->credits;
	}

	public function get_date_inscription(): ?string {
		return $this->date_inscription;
	}

	public function get_actif(): ?int {
		return $this->actif;
	}



	// ======= Opérations sur la BDD =======

	// Vérifie si l'email de l'utilisateur est déjà utilisé
	public function is_user_exist(PDO $pdo): bool {
		if (!$this->email) return false;
		
		$sql = "SELECT COUNT(*) FROM utilisateurs WHERE email = :email";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([':email' => $this->email]);
		
		return (bool)$stmt->fetchColumn();
	}
	
	// Vérifie si le pseudo de l'utilisateur est déjà utilisé
	public function is_pseudo_exist(PDO $pdo): bool {
		if (!$this->pseudo) return false;
		
		$sql = "SELECT COUNT(*) FROM utilisateurs WHERE pseudo = :pseudo";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([':pseudo' => $this->pseudo]);
		
		return (bool)$stmt->fetchColumn();
	}

	// Ajoute l'utilisateur dans la BDD
	public function add_user(PDO $pdo): bool {
		if (!$this->pseudo || !$this->email || !$this->motdepasse) return false;
		
		$sql = "INSERT INTO utilisateurs (pseudo, email, mot_de_passe, photo, credits, actif) VALUES (:pseudo, :email, :motdepasse, :photo, :credits, :actif)";
		$stmt = $pdo->prepare($sql);

		// Valeurs par défaut à l'inscription
		$this->photo = self::DEFAULT_PHOTO;
		$this->credits = self::DEFAULT_CREDITS;
		$this->actif = self::STATUS_ACTIVE;

		$ok = $stmt->execute([
			':pseudo' => $this->pseudo,
			':email' => $this->email,
			':motdepasse' => $this->motdepasse,
			':photo' => $this->photo,
			':credits' => $this->credits,
			':actif' => $this->actif
		]);
		
		if($ok)
		{
			$this->id = (int)$pdo->lastInsertId();
			return true;
		}
		
		return false;
	}
	
	// Charge les données de l'utilisateur en utilisant son ID
	public function load_user_by_id(PDO $pdo): bool {
		if (!$this->id) return false;

		$sql = "SELECT * FROM utilisateurs WHERE id = :id";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([':id' => $this->id]);
		$data = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($data) {
			$this->id = (int)$data['id'];
			$this->pseudo = $data['pseudo'];
			$this->email = $data['email'];
			$this->motdepasse = $data['mot_de_passe'];
			$this->photo = $data['photo'] ?? null;
			$this->credits = isset($data['credits']) ? (int)$data['credits'] : 0;
			$this->date_inscription = $data['date_inscription'] ?? null;
			$this->actif = isset($data['actif']) ? (int)$data['actif'] : 1;
			return true;
		}

		return false;
	}

	// Charge les données de l'utilisateur en utilisant son email
	public function load_user_by_email(PDO $pdo): bool {
		if (!$this->email) return false;
		
		$sql = "SELECT * FROM utilisateurs WHERE email = :email";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([':email' => $this->email]);
		$data = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($data) {
			$this->id = (int)$data['id'];
			$this->pseudo = $data['pseudo'];
			$this->email = $data['email'];
			$this->motdepasse = $data['mot_de_passe'];
			$this->photo = $data['photo'] ?? null;
			$this->credits = isset($data['credits']) ? (int)$data['credits'] : 0;
			$this->date_inscription = $data['date_inscription'] ?? null;
			$this->actif = isset($data['actif']) ? (int)$data['actif'] : 1;
			return true;
		}

		return false;
	}

	// Met à jour la photo de l'utilisateur dans la BDD
	public function update_photo(PDO $pdo): bool {
		if (!$this->id) return false;
		
		$sql = "UPDATE utilisateurs SET photo = :photo WHERE id = :id";
		$stmt = $pdo->prepare($sql);
		
		return $stmt->execute([
			':photo' => $this->photo,
			':id' => $this->id
		]);
	}

	// Met à jour le statut du compte (0 = suspendu, 1 = actif)
	public function update_statut(PDO $pdo): bool {
		if (!$this->id) return false;
		
		$sql = "UPDATE utilisateurs SET actif = :actif WHERE id = :id";
		$stmt = $pdo->prepare($sql);
		
		return $stmt->execute([
			':actif' => $this->actif,
			':id' => $this->id
		]);
	}
	
	// Met à jour le montant des crédits
	public function update_credits(PDO $pdo): bool {
		if (!$this->id) return false;
		
		$sql = "UPDATE utilisateurs SET credits = :credits WHERE id = :id";
		$stmt = $pdo->prepare($sql);
		
		return $stmt->execute([
			':credits' => $this->credits,
			':id' => $this->id
		]);
	}
}
?>
