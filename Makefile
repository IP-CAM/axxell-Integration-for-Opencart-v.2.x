all: clean axxell-opencart.ocmod.zip

axxell-opencart.ocmod.zip:
	zip -r axxell-opencart.ocmod.zip `find upload -name upload\*`

clean:
	rm -f axxell-opencart.ocmod.zip
