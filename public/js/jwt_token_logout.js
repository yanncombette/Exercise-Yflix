function handleLogout() {
    localStorage.removeItem('jwt-token');
}
document.querySelector('.logout').addEventListener('click', handleLogout);
