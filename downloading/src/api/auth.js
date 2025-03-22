import axios from 'axios';
const base_url = import.meta.env.VITE_BASE_URL; 

// Login endpoint pakai axios
async function login(email, password) {
    email = email.toLowerCase();
    //response ke base_url + /api/auth/login
    const response = await axios.post(base_url + '/api/auth/login', {
        email : email,
        password : password

    });
    console.log(response);

    if (response.status === 200) {
        const data = response.data;
        localStorage.setItem('token', data.token);
        localStorage.setItem('exp', data.expires_in);
        // You can store other user data in localStorage as well
        // localStorage.setItem('user', JSON.stringify(data.user));
        console.log(localStorage);
        return data;
    } else {
        console.log(response);
        throw new Error('Login failed');
    }
}

// Register Tenant endpoint menggunakan axios
async function registerTenant(body) {
    try {
        // Response ke base_url + /api/auth/register-tenant
        const response = await axios.post(`${base_url}/api/auth/register-tenant`, body);
        
        if (response.status === 201) {
            const data = response.data;
            console.log('Tenant registered successfully:', data);
            return data;
        } else {
            console.error('Registration failed:', response);
            throw new Error('Failed to register tenant');
        }
    } catch (error) {
        console.error('Error during tenant registration:', error);
        throw error;
    }
}

export { login, registerTenant };
