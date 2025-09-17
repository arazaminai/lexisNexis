import { Component, Input } from '@angular/core';
import type { Document } from '../../models/documents';
import { MatDialog } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatButtonModule } from '@angular/material/button';
import { CommonModule } from '@angular/common';
import { DetailsDialogueComponent } from './details/details-dialogue.component';
import { DeleteDialog } from './delete/delete-dialogue.component';
import { MatIconModule } from '@angular/material/icon';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { DocumentService } from '../../services/document.service';
import { DocumentSearchService } from '../../services/document-search.service';

@Component({
  selector: 'app-document-list',
  standalone: true,
  imports: [
    CommonModule,
    MatIconModule,
    MatButtonModule,
    MatPaginatorModule,
  ],
  templateUrl: './document-list.component.html',
  styleUrls: ['./document-list.component.scss']
})
export class DocumentListComponent {
  @Input() documents: Document[] = [];       // full document list
  pagedDocuments: Document[] = [];  // current page
  pageSize = 10;
  pageIndex = 0;

  constructor(
    private dialog: MatDialog,
    private snackBar: MatSnackBar,
    private docService: DocumentService,
    private searchService: DocumentSearchService
  ) {}

  ngOnInit() {
    this.searchService.results$.subscribe(results => {
      if (results === null) {
        this.loadDocuments();
        return;
      }
      this.documents = results ?? [];
      this.pageIndex = 0;
      this.updatePagedDocuments();
    });

    this.docService.refresh$.subscribe(refresh => {
        this.loadDocuments();
    });

  }

  loadDocuments() {
    
    this.docService.listDocuments().subscribe(docs => {
        this.documents = docs.map((d: any) => ({
        id: d.id,
        filename: d.filename,
        filepath: d.filepath,
        filetype: d.filetype,
        uploaded_at: d.uploaded_at,
        relevance: d.relevance,
        highlight: d.highlight
      }));
      this.updatePagedDocuments();
    });
  }

  updatePagedDocuments() {
    const start = this.pageIndex * this.pageSize;
    const end = start + this.pageSize;
    this.pagedDocuments = this.documents.slice(start, end);
  }

  onPageChange(event: PageEvent) {
    this.pageIndex = event.pageIndex;
    this.pageSize = event.pageSize;
    this.updatePagedDocuments();
  }

  openDocumentDialog(doc: Document) {
    this.dialog.open(DetailsDialogueComponent, {
      width: '500px',
      data: (doc && { host: this.docService.hostUrl, ...doc }) 
    });
  }

  confirmDelete(doc: Document) {
    const dialogRef = this.dialog.open(DeleteDialog, {
      width: '500px',
      data: { filename: doc.filename }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result === true) {
        this.deleteDocument(doc);
      }
    });
  }

  deleteDocument(doc: Document) {
    this.docService.deleteDocument(doc.id).subscribe({
      next: () => {
        this.documents = this.documents.filter(d => d.id !== doc.id);
        this.snackBar.open('File deleted successfully', 'Close', { duration: 3000 });
        this.updatePagedDocuments();
      },
      error: () => {
        this.snackBar.open('Failed to delete file', 'Close', { duration: 3000 });
      }
    });
  }
}
