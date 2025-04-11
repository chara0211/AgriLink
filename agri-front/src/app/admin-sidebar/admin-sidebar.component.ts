import { Component, OnInit } from '@angular/core';
import { AuthService } from '../auth.service'; // Assurez-vous que le chemin est correct
import { Router } from '@angular/router';

@Component({
  selector: 'app-admin-sidebar',
  standalone: false,
  templateUrl: './admin-sidebar.component.html',
  styleUrls: ['./admin-sidebar.component.scss']
})
export class AdminSidebarComponent implements OnInit {

  showUserInfo: boolean = false;
  showEditProfileModal: boolean = false;
  userName: string = '';
  userEmail: string = '';
  editableUserName: string = '';
  editableUserEmail: string = '';

  constructor(private authService: AuthService, private router: Router) {}

  ngOnInit(): void {
    const user = this.authService.user;
    if (user) {
      this.userName = user.name || '';
      this.userEmail = user.email || '';
      this.editableUserName = this.userName;
      this.editableUserEmail = this.userEmail;
    }
  }

  toggleUserInfo(): void {
    this.showUserInfo = !this.showUserInfo;
  }

  openEditProfileModal(): void {
    this.showEditProfileModal = true;
  }

  closeEditProfileModal(): void {
    this.showEditProfileModal = false;
  }

  saveProfileChanges(): void {
    this.userName = this.editableUserName;
    this.userEmail = this.editableUserEmail;
    // Ici, vous pouvez ajouter une logique pour sauvegarder les changements sur le serveur
    this.closeEditProfileModal();
  }

  logout(): void {
    this.authService.logout();
    this.router.navigate(['/login']);
  }
}
