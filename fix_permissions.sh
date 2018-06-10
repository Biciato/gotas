#criação de pastas de usuários
mkdir webroot/img/users
mkdir webroot/img/users/documents

#criação de pastas de redes
mkdir webroot/img/networks
mkdir webroot/img/networks/images

#criação de pastas de produtos
mkdir webroot/img/products

#criação de pastas de recibos
mkdir webroot/img/receipts
mkdir webroot/img/receipts/documents

#criação de pastas de brindes
mkdir webroot/img/gifts
mkdir webroot/img/gifts/images


sudo chmod 755 * -R
sudo chmod 755 -R tmp*
#Pasta de imagens deve ser permissiva
sudo chmod 776  webroot/img* -R
sudo chown www-data:www-data * -R
sudo chmod 776 tmp/* -R
sudo chown www-data:www-data * -R



#sudo rm tmp/ -rf
sudo mkdir tmp
sudo mkdir tmp/cache
sudo mkdir tmp/sessions
sudo chmod a+rw tmp/ -R
sudo chown www-data:www-data * -R
