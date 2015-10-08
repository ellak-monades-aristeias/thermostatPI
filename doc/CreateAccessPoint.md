# How to create the access point.

Connect the usb wifi to the raspberry pi.

##Install the packages:

Open a terminal and run:

    sudo apt-get -y install iw isc-dhcp-server hostapd


## Give a static ip to the raspberry pi

File `/etc/network/interfaces`:

    auto wlan0
    allow-hotplug wlan0
    iface wlan0 inet static
    address 10.0.0.254
    netmask 255.255.255.0

##Configure dhcpd

File `/etc/dhcp/dhcpd.conf`:

    ddns-update-style none;
    authoritative;
    log-facility local7;
    subnet 10.0.0.0 netmask 255.255.255.0 {
      range 10.0.0.1 10.0.0.253;
      option broadcast-address 10.0.0.255;
      option routers 10.0.0.254;
      default-lease-time 600;
      max-lease-time 7200;
      option domain-name "local";
      option domain-name-servers 8.8.8.8, 8.8.4.4;
    }

File `/etc/default/isc-dhcp-server`:

    INTERFACES="wlan0"

## Configure hostapd

Change the `MYSSID` and `MYPASS` entries.

File `/etc/hostapd/hostapd.conf`:

    interface=wlan0
    driver=nl80211
    ssid=MYSSID
    hw_mode=g
    channel=6
    macaddr_acl=0
    auth_algs=1
    ignore_broadcast_ssid=0
    wpa=2
    wpa_passphrase=MYPASS
    wpa_key_mgmt=WPA-PSK
    wpa_pairwise=TKIP
    rsn_pairwise=CCMP

File `/etc/default/hostapd`:

    DAEMON_CONF="/etc/hostapd/hostapd.conf"

##Restart services

Run:

    sudo ifconfig wlan0 192.168.255.254 netmask 255.255.0.0
    sudo service isc-dhcp-server restart
    sudo service hostapd restart
