# VárosiFák API
## Általános struktúra (CRUD)
### GET kérések (**R**etrieve)
A GET Requestek az URL-ben lévő paramétereket dolgozza fel.

### POST kérések (**C**reate)
A POST Requestek JSON formátumban bejövő adatokat dolgozzák fel.
- Elsősorban: Adatok felvitele

### DELETE kérések (**D**elete)
A DELETE kérések az URL-ben lévő paraméterek alapján töröl rekordot/
rekordokat az adatbázisból.

### PATCH kérések (**U**pdate)
A PATCH kérések során a JSON-ban kapott adatok alapján frissít rekordot/rekordokat az adatbázisban.

### PUT kérések (*Upload*)
A PUT kérések során Multipart adatokat lehet felküldeni a szerverre. Például képeket.
A CRUD szerkezetbe nem tartozik bele, viszont tulajdonképpen egy speciális CREATE-ről van szó.