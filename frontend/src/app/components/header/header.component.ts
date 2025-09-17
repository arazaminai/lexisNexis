import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatIconModule } from '@angular/material/icon';
import { MatDialog } from '@angular/material/dialog';
import { debounceTime, distinctUntilChanged, switchMap } from 'rxjs/operators';
import { Observable } from 'rxjs';
import { UploadDocumentComponent } from '../upload-document/upload-document.component';
import { DocumentService } from '../../services/document.service';
import { SearchBarComponent } from '../search-bar/search-bar.component';
import { FormControl } from '@angular/forms';

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [
    CommonModule,
    MatToolbarModule,
    MatIconModule,
    SearchBarComponent 
  ],
  templateUrl: './header.component.html',
  styleUrl: './header.component.scss'
})
export class HeaderComponent {
  private dialog = inject(MatDialog);

  searchControl = new FormControl('');
  filteredResults!: Observable<any[]>;

  constructor(
    private docService: DocumentService
  ) {
    this.filteredResults = this.searchControl.valueChanges.pipe(
      debounceTime(300),
      distinctUntilChanged(),
      switchMap(query => this.docService.searchDocuments(query || ''))
    );
  }

  openUploadDialog() {
    this.dialog.open(UploadDocumentComponent, {
      width: '900px',
    }).componentInstance.uploadComplete.subscribe(() => {
      this.dialog.closeAll();
      this.docService.setRefresh(true);
    });
  }
}
