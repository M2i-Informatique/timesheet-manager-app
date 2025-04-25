# Dubocq – Application de pointage & reporting

## Table des matières

- [Introduction](#introduction)  
- [Fonctionnalités principales](#fonctionnalités-principales)  
- [Architecture générale](#architecture-générale)  
  - [MVC & Livewire](#mvc--livewire)  
  - [Services & Exports](#services--exports)
  - **[Personnalisation des formules métier](#personnalisation-des-formules-métier)**
  - [Observers & Logging](#observers--logging)  
  - [Notifications & Emails](#notifications--emails)  
- [Modèles de données & relations](#modèles-de-données--relations)  
  - [User](#user)  
  - [Worker & Interim](#worker--interim)  
  - [Project](#project)  
  - [Zone](#zone)  
  - [Relations polymorphiques](#relations-polymorphiques)  
  - [Settings](#settings)  
- [Authentification & autorisations](#authentification--autorisations)  
- [Installation & configuration](#installation--configuration)  
- [Utilisation](#utilisation)  
- [Tests & qualité](#tests--qualité)  
- [Déploiement (local)](#déploiement-local)  
- [Contribuer](#contribuer)  
- [Licence](#licence)  

---

## Introduction

Cette application permet à un client de l’entreprise Dubocq de :

- **Pointer** ses ouvriers et intérimaires sur chantiers via une interface de type “tableur” (Handsontable non commercial).  
- **Suivre** en temps réel les heures et coûts (KPI) par salarié et par chantier.  
- **Exporter** chaque mois les feuilles de pointage au format Excel.  

---

## Fonctionnalités principales

1. **Pointage**  
   - Saisie des heures jour/nuit par salarié (worker ou intérimaire), par chantier et par date.  
   - Interface type Excel (Handsontable) avec calculs inline et total automatique.

2. **Reporting**  
   - Dashboards Livewire pour visualiser :  
     - Coûts totaux et heures totales (workers vs intérims).  
     - Répartition par chantier (catégories “mh” / “go” / “other”).  
   - Services métiers dédiés pour calculs :  
     - `Services\Hours\ProjectHoursService` & `WorkerHoursService`  
     - `Services\Costs\CostsCalculator`, `ProjectCostsService`, `WorkerCostsService`

3. **Exports Excel**  
   - `BlankMonthlyExport` : gabarit vierge par chantier.  
   - `WorkerMonthlyExport` : pointage détaillé par ouvrier et par projet.  
   - Stylisation et mise en forme (bordures, weekends grisés, jours fériés colorés, formules de totaux).

4. **Paramètres métiers**  
   - Table `settings` stockant clés/valeurs variables :  
     - `basket` (panier repas), `rate_charged` (taux de charge), avec périodes de validité.

---

## Architecture générale

### MVC & Livewire

- **Laravel 11+** avec structure MVC classique (`app/Http/Controllers`, `app/Models`, `resources/views`).  
- **Livewire** pour composants réutilisables (tables de données, navigation, activity logs), pagination Tailwind via `Paginator::useTailwind()`.

### Services & Exports

- **Services** dans `app/Services/Hours` et `app/Services/Costs` :  
  - **Heures** :  
    - `ProjectHoursService`  
    - `WorkerHoursService`  
  - **Coûts** :  
    - `CostsCalculator`  
    - `ProjectCostsService`  
    - `WorkerCostsService`  

  > **Personnalisation des calculs**  
  > Toute modification des formules métier (panier repas, taux de charge, calcul jour/nuit, etc.) doit être effectuée dans ce répertoire (`app/Services/...`).

- **Exports** (`app/Exports`) implémentant `FromArray`, `WithStyles`, `WithEvents` :  
  - `BlankMonthlyExport` : gabarit vierge par chantier.  
  - `WorkerMonthlyExport` : pointage détaillé par ouvrier et par projet.  
  - Mise en forme : bordures, weekends grisés, jours fériés colorés, formules Excel.

### Personnalisation des formules métier

Toutes les règles de calcul (heures et coûts) sont centralisées dans **`app/Services`**. Pour adapter ou modifier une formule :

1. **Coûts** (`app/Services/Costs`)  
   - **`CostsCalculator.php`**  
     - Constructeur : récupère `basket` et `rate_charged` depuis `Setting::getValue()`  
     - `calculateHourlyDayCost(Worker $w, Project $p)`  
     - `calculateHourlyNightCost(Worker $w, Project $p)`  
     - `calculateTotalCostForOneWorker(...)`  
     - `calculateTotalCostForProject(...)`  
   - **`ProjectCostsService.php`** et **`WorkerCostsService.php`**  
     - Orchestrent les appels au `CostsCalculator` et formatent la sortie.

2. **Heures** (`app/Services/Hours`)  
   - **`ProjectHoursService.php`** (`getProjectHours($id, $cat, $start, $end)`)  
   - **`WorkerHoursService.php`**  (`getWorkerHours($id, $cat, $start, $end)`)

> **Exemple** : pour changer le calcul du panier repas, ajustez la ligne  
> ```php
> $this->baseBasketValue = (float) Setting::getValue('basket', 11);
> ```  
> dans le constructeur de `CostsCalculator.php`. <br>
> Ici, la valeur du panier repas (`basket`) est récupérée depuis la base de données. Si aucune valeur n'est définie, la valeur par défaut de `11` est utilisée.

### Observers & Logging

- **ProjectObserver** (`app/Observers/ProjectObserver.php`) :  
  - Assigne automatiquement zone et catégorie d’un chantier lors de la création ou de la mise à jour.  

- **ActivityLogServiceProvider** (`app/Providers/ActivityLogServiceProvider.php`) :  
  - Intercepte l’enregistrement des logs Spatie Activitylog pour associer l’utilisateur en cours comme causer.  
  - Active la pagination Tailwind :  
    ```php
    Paginator::useTailwind();
    ```

### Notifications & Emails

- **Laravel Fortify** pour flux d’authentification (inscription, mot de passe oublié, vérification d’email).  
- **Notifications** personnalisées :  
  - `CustomResetPassword`  
  - `CustomVerifyEmail`  
  - Envoi via SendGrid (clés à définir dans `.env`).

---

## Modèles de données & relations

### User

- Modèle étendu de Laravel : ajout de `first_name` & `last_name`.  
- Permissions & rôles gérés par Spatie.

### Worker & Interim

- **Worker** (`workers`) :  
  - `first_name`, `last_name`  
  - `category` (`worker` / `etam`)  
  - `contract_hours`, `monthly_salary`  
  - `status` (`active` / `inactive`)

- **Interim** (`interims`) :  
  - `agency`, `hourly_rate`  
  - `status` (`active` / `inactive`)

### Project

- Table `projects` :  
  - `code` (int), `category` (`mh` / `go` / `other`)  
  - `name`, `address`, `city`, `distance`  
  - `status` (`active` / `inactive`)  
  - `zone_id` (nullable, FK → `zones`)

### Zone

- Table `zones` :  
  - `name`, `min_km`, `max_km` (nullable), `rate`

### Relations polymorphiques

- **projectables** : relie polymorphiquement `workers` & `interims` à `projects`.  
- **time_sheetables** : pointages jour/nuit par entité (Worker ou Interim), lié au chantier.

### Settings

- Table `settings` :  
  - `key` / `value`  
  - `start_date` / `end_date` (nullable)  
  - Historise les variables métier (panier repas, taux de charge…).

---

## Authentification & autorisations

- **Fortify** gère l’authentification.  
- **Spatie Permission** :  
  - Rôles : `driver`, `leader`, `admin`, `super-admin`.  
- **Middleware** `CheckRole` :  
  ```php
  // app/Http/Middleware/CheckRole.php
  public function handle(Request $request, Closure $next, $role)
  {
     if (!$request->user() || !$request->user()->hasRole($role)) {
         abort(403, 'Accès non autorisé.');
     }
     return $next($request);
  }
  ```

---

## Installation & configuration

1. `git clone <url>.git && cd dubocq-pointage`  
2. `cp .env.example .env` → remplir DB_* et `SENDGRID_API_KEY`  
3. `composer install && npm install && npm run dev`  
4. `php artisan migrate --seed`  
5. `php artisan serve`

---

## Utilisation

- **Login** (Fortify)  
- **Tracking** → choisir chantier & salarié → saisir heures  
- **Reporting** → filtres → KPI  
- **Exports** → sélectionner → télécharger Excel

---

## Tests & qualité

- **PHPUnit** / **Pest** (`tests/Feature`, `tests/Unit`)  
- PSR-12

--- 

## Déploiement (local)

Le fichier docker-compose.yml pour le développement local :

```yaml
services:
  db:
    image: postgres:16
    environment:
      POSTGRES_DB: ${DB_DATABASE}
      POSTGRES_USER: ${DB_USERNAME}
      POSTGRES_PASSWORD: ${DB_PASSWORD}
    ports:
      - "5432:${DB_PORT}"
    volumes:
      - pgdata:/var/lib/postgresql/data
    networks:
      - dubocq_network

  pgadmin:
    image: dpage/pgadmin4
    container_name: pgadmin
    environment:
      PGADMIN_DEFAULT_EMAIL: ${PGADMIN_DEFAULT_EMAIL}
      PGADMIN_DEFAULT_PASSWORD: ${PGADMIN_DEFAULT_PASSWORD}
    ports:
      - "5050:80"
    depends_on:
      - db
    networks:
      - dubocq_network

networks:
  dubocq_network:
volumes:
  pgdata:
```
---

## Contribuer
- Fork du projet
- Créer une branche (feature/ma-fonctionnalite)
- PR avec description des changements

---

## Licence
Licence propriétaire (closed-source)

>Contrat propriétaire
>
> « La société Dubocq concède au Client un droit non exclusif, non transférable et > non sublicenciable d’utiliser le logiciel en interne. »