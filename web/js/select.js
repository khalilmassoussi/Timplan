$("document").ready(function()
{
    $(".Client").change(function(){
        $.ajax({
            type: 'get',
            url: Routing.generate('InfoClient', {id: $(this).val() }),
//            url: 'http://localhost/portailtimsoft/web/app_dev.php/InfoClient/'+ $(this).val() ,
            success: function (data)
            {
                $('label[for="codeClient"]').text(data.codeClient);
                $('label[for="telephone"]').text(data.telephone);
                $('label[for="adresse"]').text(data.adresseClient);
            }
        });
    });
    $(".IDFeuille").change(function(){
        $.ajax({
            type: 'get',
            url: Routing.generate('InfoIntervention', {id: $(this).val() }),
            success: function (data)
            {
                $('label[for="raisonSocialeRapport"]').text(data.raisonSociale);
                $('label[for="codeClientRapport"]').text(data.codeClient);
                $('label[for="telephoneRapport"]').text(data.telephone);
                $('label[for="adresseClientRapport"]').text(data.adresseClient);
                $('label[for="dateInterventionRapport"]').text(data.dateIntervention.date);
                $('label[for="lieuInterventionRapport"]').text(data.lieuIntervention);
                $('label[for="nomIntervenantRapport"]').text(data.nomIntervenant);
                $('label[for="prenomIntervenantRapport"]').text(data.prenomIntervenant);
                $('label[for="qualiteIntervenantRapport"]').text(data.qualiteIntervenant);
            }
        });
    });
// $(".Client").change(function(){
//        $.ajax({
//            type: 'get',
//            url: 'http://localhost/portailtimsoft/web/app_dev.php/InfoClient/'+ $(this).val(),
//            success: function (data)
//            {
//                $('label[for="codeClient"]').text(data.InfoClient.codeClient);
//                $('label[for="telephone"]').text(data.InfoClient.telephone);
//                $('label[for="adresse"]').text(data.InfoClient.adresseClient);
//            }
//        });
//    });
});