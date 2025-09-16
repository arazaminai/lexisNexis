import { Component } from '@angular/core';
import { DocumentService } from './services/document.service';
import { DocumentListComponent } from './components/document-list/document-list.component';
import { SearchBarComponent } from './components/search-bar/search-bar.component';
import { UploadDocumentComponent } from './components/upload-document/upload-document.component';
import { HeaderComponent } from './components/header/header.component';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    SearchBarComponent, 
    DocumentListComponent, 
    HeaderComponent,
    UploadDocumentComponent,
  ],
  templateUrl: './app.component.html',
  styleUrl: './app.component.scss'
})
export class AppComponent {
  results: any[] = [];
  selectedDoc: any = null;


  constructor(private docService: DocumentService) {}

  ngOnInit() {
    this.onUploadComplete();
  }

  onUploadComplete() {
    this.docService.listDocuments().subscribe(res => {
      this.results = res;
    });
  }

  onSelectDocument(doc: any) {
    this.selectedDoc = doc;
  }
}
