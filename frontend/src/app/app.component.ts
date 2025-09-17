import { Component } from '@angular/core';
import { DocumentListComponent } from './components/document-list/document-list.component';
import { HeaderComponent } from './components/header/header.component';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    HeaderComponent,
    DocumentListComponent, 
  ],
  templateUrl: './app.component.html',
})
export class AppComponent { }
