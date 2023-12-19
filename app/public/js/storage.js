// app storage
function appendStorage(key, val) {
    const all = getStorage(key);
    all.push(val);
    localStorage.setItem(key, JSON.stringify(all));
}
function getStorage(key) {
    return JSON.parse(localStorage.getItem(key)) || [];
}
function checkStorage(key, val) {
    const all = getStorage(key);
    return all.includes(val);
}
// my signed petitions
const my_signed_petitions_local_storage_key = 'signed-petitions';
function getSignedPetitions() {
    return getStorage(my_signed_petitions_local_storage_key);
}
function appendSignedPetition(petition_code) {
    appendStorage(my_signed_petitions_local_storage_key, petition_code);
}
function isSignedPetition(petition_code) {
    return checkStorage(my_signed_petitions_local_storage_key, petition_code);
}
// my petitions
const my_created_petitions_local_storage_key = 'my-petitions';
function getMyPetitions() {
    return getStorage(my_created_petitions_local_storage_key);
}
function appendMyPetition(petition_code) {
    appendStorage(my_created_petitions_local_storage_key, petition_code);
}
function isMyPetition(petition_code) {
    return checkStorage(my_created_petitions_local_storage_key, petition_code);
}