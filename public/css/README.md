# Structure CSS - TaskFlow

## Organisation des fichiers CSS

### Fichiers de base
- **`variables.css`** - Variables CSS globales (couleurs, espacements)
- **`base.css`** - Styles de base (reset, body, typographie, classes utilitaires)
- **`animations.css`** - Toutes les animations et keyframes
- **`components.css`** - Composants réutilisables (cosmic-card, stellar-btn, badges, etc.)
- **`navigation.css`** - Navigation, sidebar, menus
- **`forms.css`** - Styles des formulaires et inputs

### Fichiers spécifiques par page
- **`calendar.css`** - Styles spécifiques au calendrier
- **`dashboard.css`** - Styles du tableau de bord
- **`tasks.css`** - Styles de gestion des tâches
- **`homepage.css`** - Styles de la page d'accueil

### Fichier principal
- **`main.css`** - Importe tous les fichiers de base dans l'ordre correct

## Utilisation dans les templates

### Template de base (`base.html.twig`)
```html
<link href="./css/main.css" rel="stylesheet">
```

### Templates spécifiques
```html
{% block css %}
{{ parent() }}
<link href="./css/calendar.css" rel="stylesheet">
{% endblock %}
```

## Hiérarchie d'importation

1. Variables CSS
2. Styles de base
3. Animations
4. Composants
5. Navigation
6. Formulaires
7. Styles spécifiques (selon la page)

## Avantages de cette structure

✅ **Maintenabilité** - Code organisé et facile à modifier
✅ **Performance** - Chargement optimisé selon les besoins
✅ **Réutilisabilité** - Composants modulaires
✅ **Lisibilité** - Structure claire et logique
✅ **Évolutivité** - Facile d'ajouter de nouveaux styles

## Conventions de nommage

- **Variables** : `--nom-variable`
- **Composants** : `.cosmic-nom`, `.stellar-nom`
- **États** : `.active`, `.hover`, `.current-day`
- **Responsive** : Media queries dans chaque fichier concerné