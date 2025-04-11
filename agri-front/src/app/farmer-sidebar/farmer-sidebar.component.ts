import { Component, OnInit } from '@angular/core';
import { AuthService } from '../auth.service';
import { Router } from '@angular/router';


@Component({
  selector: 'app-farmer-sidebar',
  standalone: false,
  
  templateUrl: './farmer-sidebar.component.html',
  styleUrl: './farmer-sidebar.component.scss'
})
export class FarmerSidebarComponent implements OnInit{
  showUserInfo: boolean = false;
  userName: string = '';
  userEmail: string = '';
  constructor(public authService: AuthService, private router: Router) { }

  ngOnInit(): void {
    // Vérifiez que les données de l'utilisateur sont présentes avant de les assigner
    const user = this.authService.user; // Utilise directement la propriété 'user' du service
    if (user) {
      this.userName = user.name || '';  // Assurez-vous que 'name' est présent dans les données de l'utilisateur
      this.userEmail = user.email || '';  // Assurez-vous que 'email' est présent
    }
  }

  toggleUserInfo(): void {
    this.showUserInfo = !this.showUserInfo;
  }

  logout(): void {
    this.authService.logout(); // Appel de la méthode logout
    this.router.navigate(['/login']); // Redirection vers la page de connexion
  }

}
