# thermostatPI

ThermostatPi is a project that contains the code and the instructions in order to make a smart thermostat for the house.
It uses two DHT22 sensors.
The first measures the inner temperature of the house.
The other measures the outer temperature of the house.
If the outer is above the inner, then the thermostat is not activated.
The temperature thresholds that the thermostat is activated are configured through a web interface
Moreover, the user can se the consumption in hours and set an upper limit per day.

Documentation for [users](README_USERS.md).

Documentation for [developers](README_DEVELOPERS.md).
 
# Installation

The installation instructions can be found in the doc folder: [doc/README.md](doc/README.md)

# Hardware

What is needed:

* 1 * Raspberry pi
* 2 * [DHT22 sensors](https://www.adafruit.com/products/385)
* 1 * [relay](https://www.sparkfun.com/products/11042)
* 2 * 4.7K resistance
* 1 * usb wifi
* cables

# License

See: [LICENSE](LICENSE)

# Παραδωτέα


| Παραδοτέο | Σύντομη περιγραφή | URL |
|-----------|-------------------|-----|
| 1 | Αγορά υλικού: Raspberry pi, αισθητήρες θερμοκρασίας, ρελέ, κεραία ασύρματου δικτύου. | https://github.com/ellak-monades-aristeias/thermostatPI/blob/master/README.md |
| 2 | Δυνατότητα 7 | https://github.com/ellak-monades-aristeias/thermostatPI/blob/master/doc/CreateAccessPoint.md,  https://github.com/ellak-monades-aristeias/thermostatPI/blob/master/doc/README.md |
| 3 | Δυνατότητα 1 | https://github.com/ellak-monades-aristeias/thermostatPI/blob/master/src/opt/thermostatPi/thermostatPi.py |
| 4 | Δυνατότητα 2 | https://github.com/ellak-monades-aristeias/thermostatPI/blob/master/src/var/www/config.php, https://github.com/ellak-monades-aristeias/thermostatPI/blob/master/src/var/www/thermostatConfiguration.php, https://github.com/ellak-monades-aristeias/thermostatPI/blob/master/src/var/www/changePassword.php, https://github.com/ellak-monades-aristeias/thermostatPI/blob/master/src/var/www/viewThresholds.php |
| 5 | Δυνατότητα 3 | https://github.com/ellak-monades-aristeias/thermostatPI/blob/master/src/opt/thermostatPi/thermostatPi.py |
| 6 | Δυνατότητα 4 | https://github.com/ellak-monades-aristeias/thermostatPI/blob/master/src/var/www/viewConsumption.php |
| 7 | Δυνατότητα 5 | https://github.com/ellak-monades-aristeias/thermostatPI/blob/master/src/opt/thermostatPi/thermostatPi.py |
| 8 | Δυνατότητα 6 | https://github.com/ellak-monades-aristeias/thermostatPI/blob/master/src/var/www/changeMaxConsumption.php |

Δυνατότητες:

1. Ο θερμοστάτης θα ενεργοποιείται αν πέσει κάτω από κάποια θερμοκρασία και θα μένει ενεργοποιημένος μέχρι να φτάσει κάποια θερμοκρασία. Για παράδειγμα, έστω ότι ο θερμοστάτης έχει χαμηλή θερμοκρασία 20 βαθμούς και μέγιστη 25 βαθμούς. Αν η θερμοκρασία του χώρου είναι 22 βαθμούς, δεν θα ενεργοποιηθεί. Αν είναι 19 βαθμούς θα ενεργοποιηθεί και θα παραμείνει ενεργοποιημένος μέχρι να φτάσει τους 25 βαθμούς.
2. Η μέγιστη και ελάχιστη θερμοκρασία του θερμοστάτη θα μπορεί να ρυθμιστεί και να είναι διαφορετική ανά ώρα ή μέρα (ή κάποια άλλη υποδιαίρεση του χρόνου που επιθυμεί ο χρήστης).
3. Ο θερμοστάτης θα μετρά πόσες ώρες έμεινε ενεργοποιημένος (οπότε ο χρήστης θα μπορεί να δει πόσο πετρέλαιο ή ρεύμα έκαψε).
4. Ο θερμοστάτης θα μπορεί να παράγει γράφημα με την ενεργοποίηση του θερμοστάτη και την θερμοκρασία του χώρου.
5. Ο θερμοστάτης θα μπορεί να έχει παραπάνω από έναν αισθητήρες θερμοκρασίας, οπότε να ενεργοποιείται αν κάποιο είναι κάτω από κάποια όρια ή να μην ενεργοποιείται αν κάποιος είναι πάνω από κάποιο όριο. Για παράδειγμα, αν έχουμε έναν εσωτερικό και έναν εξωτερικό και η θερμοκρασία που μετρά ο εξωτερικός είναι πάνω από την θερμοκρασία που μετρά ο εσωτερικός, να μην ενεργοποιείται ο θερμοστάτης γιατί αναμένεται να ανέβει η θερμοκρασία και στον εσωτερικό (θα μεταφερθεί θερμότητα από έξω στον εσωτερικό χώρο).
6. Θα μπορεί να υπάρχει ένα άνω όριο στο σύνολο των ωρών χρήσς του θερμοστάτη ανά μέρα (ή κάποια άλλη χρονική υποδιαίρεση). Αν το σύνολο ωρών που είναι ενεργοποιημένος ο θερμοστάτης ξεπεράσει το όριο, τότε δεν ενεργοποιείται παραπάνω. Με αυτόν τον τρόπο ο χρήστης μπορεί να θέσει ένα άνω όριο στο κόστος θέρμανσης.
7. Οι ρυθμίσεις θα γίνονται μέσω web interface. Ο θερμοστάτης θα έχει και μία κεραία η οποία θα δημιουργεί ένα ασύρματο τοπικό δίκτυο και στο οποίο θα μπορεί να συνδεθεί ο χρήστης με το τηλέφωνό του.
