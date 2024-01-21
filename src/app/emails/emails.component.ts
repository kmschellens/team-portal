import { Component } from '@angular/core';
import { EmailsService } from '../core/services/emails.service';

@Component({
  selector: 'tp-emails',
  templateUrl: './emails.component.html',
  styleUrls: ['./emails.component.scss']
})
export class EmailsComponent {

  emails: any;
  loading: boolean;
  errorMessage: string;
  

  constructor(private emailsService: EmailsService) {}


  async ngOnInit() {
    this.getEmails();
  }
  
  async getEmails() {
    this.loading = true;
    this.emailsService.getEmails().subscribe(data => {
      this.loading = false;
      this.emails = data; //  
      return data;
    }, error => {
      this.errorMessage = error.error.message
      console.error('error getting emails', error)
      this.loading = false
    })
  }

}
