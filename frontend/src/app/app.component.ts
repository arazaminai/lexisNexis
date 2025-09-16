import { Component } from '@angular/core';
import { DocumentService } from './services/document.service';
import { DocumentListComponent } from './components/document-list/document-list.component';
import { SearchBarComponent } from './components/search-bar/search-bar.component';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [SearchBarComponent, DocumentListComponent],
  templateUrl: './app.component.html',
  styleUrl: './app.component.scss'
})
export class AppComponent {
  results: any[] = [];

  constructor(private docService: DocumentService) {}

  ngOnInit() {
    this.docService.listDocuments().subscribe(res => {
      this.results = res;
    });
  }

  onSearch(query: string) {
    if (!query.trim()) return;
    this.docService.searchDocuments(query).subscribe(res => {
      this.results = res.results;
    });
  }
}
