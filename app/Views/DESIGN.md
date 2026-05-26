---
name: KASHALA Trans Management
colors:
  background: '#f6f7f9'
  surface: '#ffffff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f8fafc'
  surface-container: '#f1f5f9'
  surface-container-high: '#e2e8f0'
  surface-container-highest: '#cbd5e1'
  on-surface: '#111827'
  on-surface-variant: '#475569'
  outline: '#64748b'
  outline-variant: '#d0d7de'
  primary: '#1d4ed8'
  primary-hover: '#1e40af'
  on-primary: '#ffffff'
  primary-container: '#dbeafe'
  on-primary-container: '#1e3a8a'
  secondary: '#334155'
  secondary-container: '#e2e8f0'
  error: '#b91c1c'
  error-container: '#fee2e2'
  success: '#047857'
  success-container: '#d1fae5'
  warning: '#b45309'
  warning-container: '#fef3c7'
typography:
  h1:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
    letterSpacing: 0
  h2:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '700'
    lineHeight: 28px
    letterSpacing: 0
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
    letterSpacing: 0
  body-sm:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '400'
    lineHeight: 18px
    letterSpacing: 0
  label-caps:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '700'
    lineHeight: 16px
    letterSpacing: 0
rounded:
  sm: 4px
  DEFAULT: 6px
  lg: 8px
  xl: 12px
  full: 9999px
spacing:
  base: 4px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 32px
  gutter: 16px
  margin: 24px
---

## Direction

KASHALA Trans utilise un design de gestion métier : sobre, dense, lisible et conçu pour une utilisation prolongée au guichet ou sur desktop. L’interface doit aider l’agent à trouver rapidement l’information utile sans décor inutile.

Le style s’inspire davantage d’un outil d’exploitation que d’une page marketing : navigation compacte, tableaux lisibles, formulaires structurés, actions visibles et états d’erreur explicites.

## Palette

La palette principale est volontairement neutre :

- fond application : `#f6f7f9`
- surfaces et cartes : `#ffffff`
- bordures : `#d0d7de`
- texte principal : `#111827`
- texte secondaire : `#475569`

La couleur primaire `#1d4ed8` est réservée aux actions fortes, à l’état actif de navigation et aux focus. Les couleurs sémantiques sont limitées aux états métier : succès, avertissement, erreur et information.

## Accessibilité

Tous les éléments interactifs doivent avoir :

- un état `focus-visible` clair ;
- un contraste lisible ;
- une hauteur minimale confortable ;
- un libellé visible ou un `aria-label` pour les icônes seules.

Les alertes utilisent `role="alert"` ou `role="status"` selon le cas. Les contrôles du menu mobile exposent `aria-expanded`, `aria-hidden` et se ferment avec `Escape`.

## Typographie

La police unique est Inter. Les tailles restent compactes pour favoriser les tableaux et formulaires métier :

- titre page : 24px / 32px / 700
- titre section : 18px / 28px / 700
- corps : 14px / 20px
- secondaire : 13px / 18px

Le letter spacing reste à `0` pour éviter les problèmes de lisibilité et de débordement en français.

## Layout

La navigation principale reste fixe en haut. Les vues utilisent une largeur maximale large sur desktop afin de mieux exploiter les tableaux, calendriers, réservations et rapports.

Les zones de contenu doivent être composées de surfaces simples :

- cartes avec bordure 1px ;
- ombre très légère ;
- rayon de 8px pour les composants ;
- sections non imbriquées inutilement.

## Composants

### Boutons

- primaire : fond bleu, texte blanc ;
- secondaire : fond blanc, bordure neutre ;
- destructif : rouge uniquement pour suppression ou erreur forte ;
- icône seule : carré 36-40px, `aria-label` obligatoire.

### Formulaires

Les champs gardent un fond blanc, une bordure neutre et un focus bleu de 3px. Les labels restent proches du champ, sans majuscules décoratives forcées.

### Tables

Les tables privilégient :

- en-tête clair ;
- lignes séparées horizontalement ;
- hover discret ;
- alignement numérique à droite ;
- pagination visible quand nécessaire.

### Statuts

Les statuts utilisent des badges sobres avec une teinte faible et un texte fortement contrasté. Le badge ne doit pas être le seul signal si l’état est critique.
