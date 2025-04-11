import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private _token: string | null = null;
  private _user: any = null;

  private apiUrlRegister = 'http://localhost:8000/api/register';
  private apiUrlLogin = 'http://localhost:8000/api/login';

  constructor(private http: HttpClient) {}

  // Get token
  get token(): string | null {
    return this._token;
  }

  // Set token
  setToken(token: string): void {
    this._token = token;
    localStorage.setItem('token', token);
  }

  // Get user
  get user(): any {
    return this._user || JSON.parse(localStorage.getItem('user') || '{}');
  }

  // Set user
  setUser(user: any): void {
    this._user = user;
    localStorage.setItem('user', JSON.stringify(user));
  }

  // Registration method
  register(userData: any): Observable<any> {
    return this.http.post(this.apiUrlRegister, userData);
  }

  // Login method
  login(credentials: { email: string, password: string }): Observable<any> {
    return this.http.post(this.apiUrlLogin, credentials);
  }

  // Get user ID
  getUserId(): number {
    return this.user?.id || 0;
  }

  // Get user name
  getUserName(): string {
    return this.user?.name || '';
  }

  // **Get user email**
  getUserEmail(): string {
    return this.user?.email || '';
  }

  // Check if the user is logged in
  isLoggedIn(): boolean {
    return !!this.token && !!this.user.id;
  }

  // Logout method
  logout(): void {
    this._token = null;
    this._user = null;
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  }
}
