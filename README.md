# CFCT_APP
Tickets machine / POS

Je fais parti d'un comité des fêtes, nous organisons des manifesations avec des repas.
Pour gérer les encaissemnts nous avions un fichier excel et des carnets de tickets à souche pour chaque plat.
J'ai voulu créer une app qui permmette de gerer cela.
On enregistre les commande, on defini le moyen de paiement et ensuite on imprimme le nombre de tikcets corrspondant à la commande (3 entrées = 3 tickets, 5 plats = 5 tickets.....) le tout sur un imprimante thermique.
Vous avez compris le principe

Ce code n'est de loin pas le plus propre ou le plus efficien mais il fonctionne. Je vais le faire evoluer par la suite pour ajouter :
- multi points de ventes (id vendeur) et donc multi imprimantes
- Gestion des pré commandes, les gens peuvent commander à l'avance et venir retirer les tickets sur place avec un Qr code ou un Code à donner pour fluidifiezr le passage en caisse...
