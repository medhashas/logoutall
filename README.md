# LogoutAll

**Sign out of many websites from one page, one click at a time.**

Live site: https://logoutall.troovapp.fr

[Français ci-dessous](#français)

---

## What it does

1. You tick the websites you want to sign out of (or "Select all").
2. LogoutAll opens each site's official sign-out page in a new tab, one click per site.
3. You tick each site once you have checked that you are signed out. A counter shows what is left to finish.

For some services, a link also leads to the page where you can sign out of **all your devices**.

It is useful after working on a shared or public computer, or simply to tidy up your open sessions.

## What it does not do

LogoutAll is honest about the limits set by web browsers:

- **Nothing happens silently.** Modern browsers block hidden sign-out requests sent from another website. Every sign-out page is opened by your own click.
- **It cannot check the result.** A web page is not allowed to read another site's response. LogoutAll never claims that you are signed out: you confirm it yourself.
- **It cannot see where you are signed in.** The list is the same for everyone.
- **Some sites ask for a confirmation.** They are labelled "Confirmation needed on the site".

The idea comes from superlogout.com, which signed visitors out of everything as soon as the page loaded. LogoutAll does the opposite: you choose, you click, you check.

## Privacy

- No cookies, no tracking, no analytics, no database.
- The list of sites you tick never leaves your browser.
- The contact form sends one e-mail to the maintainer and stores nothing on the server.

## Installation

LogoutAll is a single file.

**Requirements:** a web server with PHP 7.4 or later. The contact form uses PHP's `mail()` function.

1. Copy `index.php` to the root folder of your site.
2. Open the file and adjust the settings at the top:
   - `CONTACT_TO`: the address that receives contact messages
   - `CONTACT_FROM`: the sender address (it must belong to your domain)
   - `GITHUB_URL` and `COPYRIGHT`: shown in the footer
3. Open the page in a browser.

On the first visit, the page creates a file named `.logoutall-secret.php` next to `index.php`. It holds the key that protects the contact form against robots. **Never publish this file.** It is already listed in `.gitignore`.

## Adding or fixing a site

The sites are listed in the `$sites` array at the top of `index.php`. Each entry looks like this:

```php
['id' => 'github', 'name' => 'GitHub', 'type' => 'manual',
 'url' => 'https://github.com/logout',
 'sessions' => 'https://github.com/settings/sessions'],
```

| Key | Meaning |
|---|---|
| `id` | Short unique identifier, lowercase |
| `name` | Name shown to the user |
| `type` | `direct` if opening the URL signs the user out at once, `manual` if the site asks for a confirmation |
| `url` | The site's official sign-out page |
| `sessions` | Optional: the page to sign out of all devices |

Please test a link while signed in to the site before proposing it. Websites change their sign-out pages from time to time, so reports of broken links are welcome.

## Languages

English and French. The language follows the browser, and can be forced with `?lang=en` or `?lang=fr`. To add a language, copy one block of the `$i18n` array and translate it.

## Contributing

Suggestions, new sites and broken-link reports are welcome: open an issue or a pull request, or use the contact form on the live site.

## License

See the [LICENSE](LICENSE) file.

© 2026 mH

---

## Français

**Déconnectez-vous de nombreux sites depuis une seule page, un clic à la fois.**

Site en ligne : https://logoutall.troovapp.fr

### Ce que fait l'outil

1. Vous cochez les sites dont vous voulez vous déconnecter (ou « Tout sélectionner »).
2. LogoutAll ouvre la page de déconnexion officielle de chaque site dans un nouvel onglet, un clic par site.
3. Vous cochez chaque site après avoir vérifié que vous êtes déconnecté. Un compteur indique ce qu'il reste à terminer.

Pour certains services, un lien mène aussi à la page qui permet de déconnecter **tous vos appareils**.

C'est utile après avoir utilisé un ordinateur partagé ou public, ou simplement pour faire le ménage dans vos sessions ouvertes.

### Ce qu'il ne fait pas

LogoutAll est transparent sur les limites imposées par les navigateurs :

- **Rien ne se passe en arrière-plan.** Les navigateurs récents bloquent les demandes de déconnexion cachées envoyées depuis un autre site. Chaque page de déconnexion est ouverte par votre propre clic.
- **Il ne peut pas vérifier le résultat.** Une page web n'a pas le droit de lire la réponse d'un autre site. LogoutAll n'affirme jamais que vous êtes déconnecté : c'est vous qui le confirmez.
- **Il ne peut pas voir où vous êtes connecté.** La liste est la même pour tout le monde.
- **Certains sites demandent une confirmation.** Ils portent la mention « Confirmation à faire sur le site ».

L'idée vient de superlogout.com, qui déconnectait le visiteur de tout dès l'affichage de la page. LogoutAll fait l'inverse : vous choisissez, vous cliquez, vous vérifiez.

### Vie privée

- Aucun cookie, aucun suivi, aucune statistique, aucune base de données.
- La liste des sites cochés ne quitte jamais votre navigateur.
- Le formulaire de contact envoie un e-mail au responsable du site et ne conserve rien sur le serveur.

### Installation

LogoutAll tient en un seul fichier.

**Prérequis :** un serveur web avec PHP 7.4 ou plus récent. Le formulaire de contact utilise la fonction `mail()` de PHP.

1. Copiez `index.php` à la racine de votre site.
2. Ouvrez le fichier et adaptez les réglages en haut :
   - `CONTACT_TO` : l'adresse qui reçoit les messages
   - `CONTACT_FROM` : l'adresse d'expéditeur (elle doit appartenir à votre domaine)
   - `GITHUB_URL` et `COPYRIGHT` : affichés dans le pied de page
3. Ouvrez la page dans un navigateur.

À la première visite, la page crée un fichier nommé `.logoutall-secret.php` à côté de `index.php`. Il contient la clé qui protège le formulaire de contact contre les robots. **Ne publiez jamais ce fichier.** Il figure déjà dans le `.gitignore`.

### Ajouter ou corriger un site

Les sites sont listés dans le tableau `$sites` en haut de `index.php`. Chaque entrée a cette forme :

```php
['id' => 'github', 'name' => 'GitHub', 'type' => 'manual',
 'url' => 'https://github.com/logout',
 'sessions' => 'https://github.com/settings/sessions'],
```

| Clé | Rôle |
|---|---|
| `id` | Identifiant court et unique, en minuscules |
| `name` | Nom affiché à l'utilisateur |
| `type` | `direct` si l'ouverture de l'adresse déconnecte immédiatement, `manual` si le site demande une confirmation |
| `url` | La page de déconnexion officielle du site |
| `sessions` | Facultatif : la page pour déconnecter tous les appareils |

Merci de tester un lien en étant connecté au site avant de le proposer. Les sites modifient parfois leur page de déconnexion : les signalements de liens cassés sont les bienvenus.

### Langues

Anglais et français. La langue suit celle du navigateur et peut être forcée avec `?lang=en` ou `?lang=fr`. Pour ajouter une langue, copiez un bloc du tableau `$i18n` et traduisez-le.

### Contribuer

Suggestions, nouveaux sites et signalements de liens cassés sont les bienvenus : ouvrez une « issue » ou une « pull request », ou utilisez le formulaire de contact du site.

### Licence

Voir le fichier [LICENSE](LICENSE).

© 2026 mH
